<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannerSection;
use App\Models\SpotifySection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FormController extends Controller
{
    // Constantes para melhor manutenibilidade
    private const SPOTIFY_API_BASE = 'https://api.spotify.com/v1';
    private const SPOTIFY_TOKEN_URL = 'https://accounts.spotify.com/api/token';
    private const SPOTIFY_TOKEN_CACHE_KEY = 'spotify_access_token';
    private const SPOTIFY_TOKEN_CACHE_TTL = 3500; // segundos
    private const HTTP_TIMEOUT = 30; // segundos

    // Mapeamento de tipos do Spotify para endpoints da API
    private const SPOTIFY_ENDPOINTS = [
        'track' => 'tracks',
        'artist' => 'artists',
        'album' => 'albums',
        'playlist' => 'playlists',
        'show' => 'shows', // podcasts
        'episode' => 'episodes' // episódios de podcast
    ];

    // --- MÉTODOS PARA BANNER SECTION (sem alterações) ---
    public function bannerSection()
    {
        $banner = BannerSection::first();
        return view('admin.forms.bannerSection', compact('banner'));
    }

    public function storeBannerSection(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'learn_more_link' => 'nullable|url',
        ]);
        
        $bannerSection = BannerSection::first() ?? new BannerSection();
        
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('banners', 'public');
            $bannerSection->image_path = $imagePath;
        }
        
        $bannerSection->title = $request->title;
        $bannerSection->subtitle = $request->excerpt;
        $bannerSection->learn_more_link = $request->learn_more_link;
        $bannerSection->save();
        
        return redirect()->back()->with('success', 'Seção de banner atualizada com sucesso!');
    }

    // --- MÉTODOS PARA SPOTIFY SECTION ---
    public function spotifySection()
    {
        $spotifySection = SpotifySection::first();
        return view('admin.forms.spotifySection', compact('spotifySection'));
    }

    public function storeSpotifySection(Request $request)
    {
        $request->validate([
            'embed_link' => 'required|string',
        ]);

        $spotifyInput = $request->input('embed_link');
        
        try {
            $coverImageUrl = $this->extractSpotifyCoverImage($spotifyInput);
            
            if (!$coverImageUrl) {
                return redirect()->back()->with('error', 
                    'Não foi possível obter a imagem do Spotify. Verifique o link e suas credenciais da API.'
                );
            }

            SpotifySection::updateOrCreate(
                ['id' => 1],
                [
                    'embed_link' => $spotifyInput,
                    'cover_image_url' => $coverImageUrl,
                ]
            );

            return redirect()->back()->with('success', 'Seção do Spotify atualizada com sucesso!');
            
        } catch (\Exception $e) {
            Log::error('Erro ao processar seção do Spotify', [
                'error' => $e->getMessage(),
                'input' => $spotifyInput
            ]);
            
            return redirect()->back()->with('error', 
                'Ocorreu um erro ao processar o link do Spotify. Por favor, tente novamente.'
            );
        }
    }

    // --- MÉTODOS PRIVADOS REFATORADOS PARA A API DO SPOTIFY ---
    
    /**
     * Extrai a imagem de capa de qualquer tipo de conteúdo do Spotify
     */
    private function extractSpotifyCoverImage(?string $input): ?string
    {
        if (empty($input)) {
            return null;
        }

        // Extrai tipo e ID do link do Spotify
        $spotifyData = $this->parseSpotifyUrl($input);
        if (!$spotifyData) {
            Log::warning('Spotify: URL inválida ou tipo não suportado', ['input' => $input]);
            return null;
        }

        // Obtém o token de acesso
        $accessToken = $this->getSpotifyAccessToken();
        if (!$accessToken) {
            return null;
        }

        // Busca os dados na API do Spotify
        return $this->fetchSpotifyImage($spotifyData['type'], $spotifyData['id'], $accessToken);
    }

    /**
     * Analisa a URL do Spotify e extrai tipo e ID
     */
    private function parseSpotifyUrl(string $url): ?array
    {
        // Pattern unificado para capturar tipo e ID de qualquer URL do Spotify
        $pattern = '/spotify\.com\/(?:embed\/)?(' . implode('|', array_keys(self::SPOTIFY_ENDPOINTS)) . ')\/([a-zA-Z0-9]+)/';
        
        if (preg_match($pattern, $url, $matches)) {
            return [
                'type' => $matches[1],
                'id' => $matches[2]
            ];
        }
        
        return null;
    }

    /**
     * Busca a imagem na API do Spotify
     */
    private function fetchSpotifyImage(string $type, string $id, string $accessToken): ?string
    {
        $endpoint = self::SPOTIFY_ENDPOINTS[$type] ?? null;
        if (!$endpoint) {
            Log::error('Spotify: Tipo de conteúdo não suportado', ['type' => $type]);
            return null;
        }

        try {
            $response = Http::withToken($accessToken)
                ->timeout(self::HTTP_TIMEOUT)
                ->get(self::SPOTIFY_API_BASE . "/{$endpoint}/{$id}");

            if (!$response->successful()) {
                Log::error('Spotify API: Resposta não bem-sucedida', [
                    'type' => $type,
                    'id' => $id,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();
            return $this->extractImageFromResponse($data, $type);
            
        } catch (\Exception $e) {
            Log::error('Spotify API: Exceção ao buscar dados', [
                'type' => $type,
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Extrai a URL da imagem da resposta da API baseado no tipo de conteúdo
     */
    private function extractImageFromResponse(array $data, string $type): ?string
    {
        // Diferentes tipos de conteúdo têm estruturas diferentes
        $imageData = match($type) {
            'track' => $data['album']['images'] ?? [],
            'episode' => $data['images'] ?? [],
            'show' => $data['images'] ?? [],
            default => $data['images'] ?? [] // artist, album, playlist
        };

        // Retorna a imagem de melhor qualidade (primeira no array)
        // Ou você pode escolher uma resolução específica
        return $this->selectBestImage($imageData);
    }

    /**
     * Seleciona a melhor imagem do array de imagens do Spotify
     */
    private function selectBestImage(array $images): ?string
    {
        if (empty($images)) {
            return null;
        }

        // O Spotify retorna as imagens ordenadas por tamanho (maior primeiro)
        // Você pode ajustar esta lógica conforme necessário
        
        // Opção 1: Retorna a maior imagem (primeira)
        return $images[0]['url'] ?? null;
        
        // Opção 2: Retorna uma imagem de tamanho médio (se disponível)
        // if (count($images) > 1) {
        //     return $images[1]['url'] ?? $images[0]['url'] ?? null;
        // }
        
        // Opção 3: Retorna imagem com dimensão específica
        // foreach ($images as $image) {
        //     if (($image['width'] ?? 0) == 640) {
        //         return $image['url'];
        //     }
        // }
        // return $images[0]['url'] ?? null;
    }

    /**
     * Obtém o token de acesso do Spotify (com cache)
     */
    private function getSpotifyAccessToken(): ?string
    {
        return Cache::remember(self::SPOTIFY_TOKEN_CACHE_KEY, self::SPOTIFY_TOKEN_CACHE_TTL, function () {
            $credentials = $this->getSpotifyCredentials();
            if (!$credentials) {
                return null;
            }

            try {
                $response = Http::asForm()
                    ->timeout(self::HTTP_TIMEOUT)
                    ->withHeaders([
                        'Authorization' => 'Basic ' . base64_encode(
                            $credentials['client_id'] . ':' . $credentials['client_secret']
                        ),
                    ])
                    ->post(self::SPOTIFY_TOKEN_URL, [
                        'grant_type' => 'client_credentials',
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    // Opcionalmente, ajusta o TTL do cache baseado no expires_in retornado
                    if (isset($data['expires_in'])) {
                        // Usa 95% do tempo de expiração para garantir que não expire durante o uso
                        $ttl = intval($data['expires_in'] * 0.95);
                        Cache::put(self::SPOTIFY_TOKEN_CACHE_KEY, $data['access_token'], $ttl);
                    }
                    
                    return $data['access_token'];
                }
                
                Log::error('Spotify API: Falha ao obter token de acesso', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                
            } catch (\Exception $e) {
                Log::error('Spotify API: Exceção ao obter token de acesso', [
                    'error' => $e->getMessage()
                ]);
            }
            
            return null;
        });
    }

    /**
     * Obtém as credenciais do Spotify do arquivo de configuração
     */
    private function getSpotifyCredentials(): ?array
    {
        $clientId = config('services.spotify.client_id') ?? env('SPOTIFY_CLIENT_ID');
        $clientSecret = config('services.spotify.client_secret') ?? env('SPOTIFY_CLIENT_SECRET');

        if (!$clientId || !$clientSecret) {
            Log::error('Credenciais da API do Spotify não encontradas');
            return null;
        }

        return [
            'client_id' => $clientId,
            'client_secret' => $clientSecret
        ];
    }

    public function contactPage()
    {
        return view('admin.forms.contactPage');
    }
}