<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannerSection;
use App\Models\SpotifySection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
// Adicione a linha abaixo para usar o objeto Response
use Illuminate\Http\Client\Response;

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


    // --- MÉTODOS PARA SPOTIFY SECTION (COM DEPURAÇÃO) ---

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
        
        // A função agora pode retornar um array com a URL ou com o erro
        $result = $this->getSpotifyCoverFromInput($spotifyInput);

        if (!$result['success']) {
            // Se falhou, redireciona com a MENSAGEM DE ERRO REAL da API
            return redirect()->back()->with('error', 'Falha na API do Spotify. Resposta: ' . $result['message']);
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

        // 1. Obter token de acesso
        $tokenResult = $this->getSpotifyAccessToken();
        if (!$tokenResult['success']) {
            return $tokenResult; // Retorna a mensagem de erro da obtenção do token
        }
        $accessToken = $tokenResult['token'];

        // 2. Tentar extrair ID de música ou artista
        preg_match('/(track|artist)\/([a-zA-Z0-9]+)/', $input, $matches);
        
        if (count($matches) < 3) {
            return ['success' => false, 'message' => 'Não foi possível identificar um ID de música ou artista no link fornecido.'];
        }

        $type = $matches[1];
        $id = $matches[2];
        $response = null;

        // 3. Fazer a chamada à API apropriada
        if ($type === 'track') {
            $response = Http::withToken($accessToken)->get("https://api.spotify.com/v1/tracks/{$id}");
            if ($response->successful() && !empty($response->json()['album']['images'][0]['url'])) {
                return ['success' => true, 'url' => $response->json()['album']['images'][0]['url']];
            }
        } elseif ($type === 'artist') {
            $response = Http::withToken($accessToken)->get("https://open.spotify.com/user/seuusuario3{$id}/albums", ['limit' => 1, 'include_groups' => 'album,single']);
            if ($response->successful() && !empty($response->json()['items'][0]['images'][0]['url'])) {
                return ['success' => true, 'url' => $response->json()['items'][0]['images'][0]['url']];
            }
        }

        // 4. Se chegou até aqui, algo falhou. Retornar o erro.
        $errorMessage = $response ? $response->body() : 'Nenhuma resposta da API.';
        return ['success' => false, 'message' => $errorMessage];
    }

    private function getSpotifyAccessToken(): array
    {
        // Usar Cache::remember anula a necessidade de checar a existência da chave antes.
        $tokenData = Cache::remember('spotify_access_token_data', 3500, function () {
            $clientId = env('SPOTIFY_CLIENT_ID');
            $clientSecret = env('SPOTIFY_CLIENT_SECRET');

            if (!$clientId || !$clientSecret) {
                return ['success' => false, 'message' => 'Credenciais (Client ID ou Secret) do Spotify não encontradas no .env.'];
            }

            $response = Http::asForm()->withHeaders([
                'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
            ])->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                return ['success' => true, 'token' => $response->json()['access_token']];
            }

            return ['success' => false, 'message' => $response->body()];
        });

        return $tokenData;
    }

    public function contactPage()
    {
        return view('admin.forms.contactPage');
    }
}