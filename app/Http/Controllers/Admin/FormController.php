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
    // --- MÉTODOS PARA BANNER SECTION (sem alterações) ---
    public function bannerSection()
    {
        $bannerSection = BannerSection::first();
        return view('admin.forms.bannerSection', compact('bannerSection'));
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
        $bannerSection->subtitle = $request->subtitle;
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
        $coverImageUrl = $this->getSpotifyCoverFromInput($spotifyInput);

        if (!$coverImageUrl) {
            return redirect()->back()->with('error', 'Não foi possível obter a imagem do Spotify. Verifique o link/código e se suas credenciais da API estão corretas.');
        }

        SpotifySection::updateOrCreate(
            ['id' => 1],
            [
                'embed_link' => $spotifyInput,
                'cover_image_url' => $coverImageUrl,
            ]
        );

        return redirect()->back()->with('success', 'Seção do Spotify atualizada com sucesso!');
    }


    // --- MÉTODOS PRIVADOS PARA A API DO SPOTIFY (COM CORREÇÃO PARA ARTISTAS) ---
    private function getSpotifyCoverFromInput(?string $input): ?string
    {
        if (empty($input)) {
            return null;
        }

        $accessToken = $this->getSpotifyAccessToken();
        if (!$accessToken) {
            Log::error('Spotify API: Não foi possível obter o token de acesso.');
            return null;
        }

        // Caso 1: O link é de uma MÚSICA (track)
        if (preg_match('/\/track\/([a-zA-Z0-9]+)/', $input, $trackMatches)) {
            $trackId = $trackMatches[1];
            
            try {
                $response = Http::withToken($accessToken)
                    ->timeout(30)
                    ->get("https://api.spotify.com/v1/tracks/{$trackId}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data['album']['images'][0]['url'])) {
                        return $data['album']['images'][0]['url'];
                    }
                }
                
                Log::error('Spotify API: Erro ao buscar track', [
                    'trackId' => $trackId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            } catch (\Exception $e) {
                Log::error('Spotify API: Exceção ao buscar track', [
                    'trackId' => $trackId,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Caso 2: O link é de um ARTISTA (artist)
        if (preg_match('/\/artist\/([a-zA-Z0-9]+)/', $input, $artistMatches)) {
            $artistId = $artistMatches[1];
            
            try {
                // CORREÇÃO: Usando a API correta para artistas
                $response = Http::withToken($accessToken)
                    ->timeout(30)
                    ->get("https://api.spotify.com/v1/artists/{$artistId}");

                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data['images'][0]['url'])) {
                        // Retorna a primeira imagem (geralmente a maior)
                        return $data['images'][0]['url'];
                    }
                }
                
                Log::error('Spotify API: Erro ao buscar artista', [
                    'artistId' => $artistId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            } catch (\Exception $e) {
                Log::error('Spotify API: Exceção ao buscar artista', [
                    'artistId' => $artistId,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Caso 3: O link é de um ÁLBUM (album)
        if (preg_match('/\/album\/([a-zA-Z0-9]+)/', $input, $albumMatches)) {
            $albumId = $albumMatches[1];
            
            try {
                $response = Http::withToken($accessToken)
                    ->timeout(30)
                    ->get("https://api.spotify.com/v1/albums/{$albumId}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data['images'][0]['url'])) {
                        return $data['images'][0]['url'];
                    }
                }
                
                Log::error('Spotify API: Erro ao buscar álbum', [
                    'albumId' => $albumId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            } catch (\Exception $e) {
                Log::error('Spotify API: Exceção ao buscar álbum', [
                    'albumId' => $albumId,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Caso 4: O link é de uma PLAYLIST
        if (preg_match('/\/playlist\/([a-zA-Z0-9]+)/', $input, $playlistMatches)) {
            $playlistId = $playlistMatches[1];
            
            try {
                $response = Http::withToken($accessToken)
                    ->timeout(30)
                    ->get("https://api.spotify.com/v1/playlists/{$playlistId}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data['images'][0]['url'])) {
                        return $data['images'][0]['url'];
                    }
                }
                
                Log::error('Spotify API: Erro ao buscar playlist', [
                    'playlistId' => $playlistId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            } catch (\Exception $e) {
                Log::error('Spotify API: Exceção ao buscar playlist', [
                    'playlistId' => $playlistId,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::error('Spotify API: Não foi possível extrair um ID válido.', ['input' => $input]);
        return null;
    }

    private function getSpotifyAccessToken(): ?string
    {
        return Cache::remember('spotify_access_token', 3500, function () {
            $clientId = env('SPOTIFY_CLIENT_ID');
            $clientSecret = env('SPOTIFY_CLIENT_SECRET');

            if (!$clientId || !$clientSecret) {
                Log::error('Credenciais da API do Spotify não encontradas no .env.');
                return null;
            }

            try {
                $response = Http::asForm()
                    ->timeout(30)
                    ->withHeaders([
                        'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
                    ])
                    ->post('https://accounts.spotify.com/api/token', [
                        'grant_type' => 'client_credentials',
                    ]);

                if ($response->successful()) {
                    return $response->json()['access_token'];
                }
                
                Log::error('Spotify API: Falha ao obter token de acesso.', [
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

    public function contactPage()
    {
        return view('admin.forms.contactPage');
    }
}