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
        
        // A função agora retorna um array com o status e a mensagem/URL
        $result = $this->getSpotifyCoverFromInput($spotifyInput);

        if (!$result['success']) {
            // Se falhou, redireciona com a MENSAGEM DE ERRO REAL da API
            return redirect()->back()->with('error', 'Erro da API do Spotify: ' . $result['message']);
        }

        SpotifySection::updateOrCreate(
            ['id' => 1],
            [
                'embed_link' => $spotifyInput,
                'cover_image_url' => $result['url'],
            ]
        );

        return redirect()->back()->with('success', 'Seção do Spotify atualizada com sucesso!');
    }


    // --- MÉTODOS PRIVADOS PARA A API DO SPOTIFY (COM DEPURAÇÃO) ---
    private function getSpotifyCoverFromInput(?string $input): array
    {
        if (empty($input)) {
            return ['success' => false, 'message' => 'O campo de link estava vazio.'];
        }

        $accessToken = $this->getSpotifyAccessToken();
        if (!$accessToken) {
            return ['success' => false, 'message' => 'Falha ao obter o token de acesso. Verifique as credenciais no .env.'];
        }

        // Caso 1: O link é de uma MÚSICA (track)
        if (preg_match('/\/track\/([a-zA-Z0-9]+)/', $input, $trackMatches)) {
            $trackId = $trackMatches[1];
            $response = Http::withToken($accessToken)->get("https://api.spotify.com/v1/tracks/{$trackId}");
            
            if ($response->successful() && !empty($response->json()['album']['images'][0]['url'])) {
                return ['success' => true, 'url' => $response->json()['album']['images'][0]['url']];
            }
            return ['success' => false, 'message' => 'Falha ao buscar dados da música. Resposta: ' . $response->body()];
        }

        // Caso 2: O link é de um ARTISTA (artist)
        if (preg_match('/\/artist\/([a-zA-Z0-9]+)/', $input, $artistMatches)) {
            $artistId = $artistMatches[1];
            $response = Http::withToken($accessToken)->get("https://open.spotify.com/oembed?url=0{$artistId}");

            if ($response->successful() && !empty($response->json()['images'][0]['url'])) {
                return ['success' => true, 'url' => $response->json()['images'][0]['url']];
            }
            // Retorna o corpo da resposta de erro para depuração
            return ['success' => false, 'message' => 'Falha ao buscar dados do artista. Resposta: ' . $response->body()];
        }

        return ['success' => false, 'message' => 'Não foi possível identificar um ID de música ou artista válido no link fornecido.'];
    }

    private function getSpotifyAccessToken(): ?string
    {
        return Cache::remember('spotify_access_token', 3500, function () {
            $clientId = env('SPOTIFY_CLIENT_ID');
            $clientSecret = env('SPOTIFY_CLIENT_SECRET');
            if (!$clientId || !$clientSecret) return null;

            $response = Http::asForm()->withHeaders([
                'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
            ])->post('https://accounts.spotify.com/api/token', ['grant_type' => 'client_credentials']);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }
            return null;
        });
    }

    public function contactPage()
    {
        return view('admin.forms.contactPage');
    }
}