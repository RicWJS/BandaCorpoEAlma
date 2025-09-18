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
    // --- MÉTODOS PARA BANNER SECTION ---

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
            // Se houver uma imagem antiga, pode ser interessante deletá-la aqui
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
            'title' => 'required|string|max:255',
            'embed_code' => 'required|string',
        ]);

        $embedCode = $request->input('embed_code');
        $coverImageUrl = $this->getSpotifyCoverFromEmbed($embedCode);

        if (!$coverImageUrl) {
            // Se a API falhar, continuamos salvando o embed, mas retornamos um aviso.
            return redirect()->back()->with('error', 'Não foi possível obter a capa do Spotify. Verifique o código de embed e suas credenciais da API.');
        }

        SpotifySection::updateOrCreate(
            ['id' => 1], // Sempre atualiza o primeiro e único registro
            [
                'title' => $request->input('title'),
                'embed_code' => $embedCode,
                'cover_image_url' => $coverImageUrl,
            ]
        );

        return redirect()->back()->with('success', 'Seção do Spotify atualizada com sucesso!');
    }


    // --- MÉTODOS PRIVADOS PARA A API DO SPOTIFY ---

    /**
     * Obtém a imagem da capa do Spotify em alta resolução a partir do código de embed.
     * Retorna a URL da imagem ou null em caso de falha.
     */
    private function getSpotifyCoverFromEmbed(?string $embedCode): ?string
    {
        if (empty($embedCode)) {
            return null;
        }

        // 1. Extrair o ID da música do código de embed
        preg_match('/\/track\/([a-zA-Z0-9]+)/', $embedCode, $matches);
        $trackId = $matches[1] ?? null;

        if (!$trackId) {
            Log::error('Spotify API: Não foi possível extrair o Track ID do embed code.', ['embed' => $embedCode]);
            return null;
        }

        // 2. Obter o token de acesso
        $accessToken = $this->getSpotifyAccessToken();
        if (!$accessToken) {
            Log::error('Spotify API: Falha ao obter o Access Token.');
            return null;
        }

        // 3. Fazer a chamada para a API Web do Spotify
        $response = Http::withToken($accessToken)->get("https://api.spotify.com/v1/tracks/{$trackId}");

        if ($response->successful()) {
            $trackData = $response->json();
            // A API retorna imagens em várias resoluções. O índice 0 é 640x640.
            if (!empty($trackData['album']['images'][0]['url'])) {
                return $trackData['album']['images'][0]['url'];
            }
        }

        Log::error('Spotify API: Falha na requisição para obter os dados da música.', ['status' => $response->status(), 'body' => $response->body()]);
        return null;
    }

    /**
     * Obtém um token de acesso da API do Spotify.
     * O token é armazenado em cache para otimizar requisições futuras.
     */
    private function getSpotifyAccessToken(): ?string
    {
        return Cache::remember('spotify_access_token', 3500, function () {
            $clientId = config('services.spotify.client_id');
            $clientSecret = config('services.spotify.client_secret');

            if (!$clientId || !$clientSecret) {
                Log::error('Credenciais da API do Spotify não encontradas no .env ou no arquivo de configuração.');
                return null;
            }

            $response = Http::asForm()->withHeaders([
                'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
            ])->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }

            return null;
        });
    }

    // --- OUTROS MÉTODOS ---

    public function contactPage()
    {
        // Lógica para a página de contato, se houver
        return view('admin.forms.contactPage');
    }
}