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


    // --- MÉTODOS PARA SPOTIFY SECTION (sem alterações) ---

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
            return redirect()->back()->with('error', 'Não foi possível obter a capa do Spotify. Verifique o link ou código de embed.');
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


    // --- MÉTODOS PRIVADOS PARA A API DO SPOTIFY (LÓGICA ATUALIZADA) ---
    private function getSpotifyCoverFromInput(?string $input): ?string
    {
        if (empty($input)) return null;

        $accessToken = $this->getSpotifyAccessToken();
        if (!$accessToken) return null;

        // Tenta identificar se é um link de MÚSICA (track)
        preg_match('/\/track\/([a-zA-Z0-9]+)/', $input, $trackMatches);
        if (!empty($trackMatches[1])) {
            $trackId = $trackMatches[1];
            $response = Http::withToken($accessToken)->get("https://api.spotify.com/v1/tracks/{$trackId}");
            
            if ($response->successful() && !empty($response->json()['album']['images'][0]['url'])) {
                return $response->json()['album']['images'][0]['url'];
            }
        }

        // Se não for música, tenta identificar se é um link de ARTISTA (artist)
        preg_match('/\/artist\/([a-zA-Z0-9]+)/', $input, $artistMatches);
        if (!empty($artistMatches[1])) {
            $artistId = $artistMatches[1];
            // Busca o álbum mais recente do artista
            $response = Http::withToken($accessToken)->get("https://open.spotify.com/user/seuusuario3{$artistId}/albums", [
                'limit' => 1, // Pega apenas o item mais recente
                'include_groups' => 'album,single', // Inclui tanto álbuns quanto singles
            ]);

            if ($response->successful() && !empty($response->json()['items'][0]['images'][0]['url'])) {
                return $response->json()['items'][0]['images'][0]['url'];
            }
        }

        Log::error('Spotify API: Não foi possível extrair ID de música ou artista do input.', ['input' => $input]);
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

    public function contactPage()
    {
        return view('admin.forms.contactPage');
    }
}