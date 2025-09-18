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


    // --- MÉTODOS PRIVADOS PARA A API DO SPOTIFY (COM AUMENTO DE TIMEOUT) ---
    private function getSpotifyCoverFromInput(?string $input): ?string
    {
        if (empty($input)) {
            return null;
        }

        $accessToken = $this->getSpotifyAccessToken();
        if (!$accessToken) {
            return null;
        }

        // Caso 1: O link é de uma MÚSICA (track)
        if (preg_match('/\/track\/([a-zA-Z0-B]+)/', $input, $trackMatches)) {
            $trackId = $trackMatches[1];
            // AUMENTANDO O TIMEOUT PARA 30 SEGUNDOS
            $response = Http::withToken($accessToken)->timeout(30)->get("https://api.spotify.com/v1/tracks/{$trackId}");
            
            if ($response->successful() && !empty($response->json()['album']['images'][0]['url'])) {
                return $response->json()['album']['images'][0]['url'];
            }
        }

        // Caso 2: O link é de um ARTISTA (artist)
        if (preg_match('/\/artist\/([a-zA-Z0-B]+)/', $input, $artistMatches)) {
            $artistId = $artistMatches[1];
            // AUMENTANDO O TIMEOUT PARA 30 SEGUNDOS
            $response = Http::withToken($accessToken)->timeout(30)->get("https://open.spotify.com/oembed?url=0{$artistId}");

            if ($response->successful() && !empty($response->json()['images'][0]['url'])) {
                return $response->json()['images'][0]['url'];
            }
        }

        Log::error('Spotify API: Não foi possível extrair um ID de música ou artista válido.', ['input' => $input]);
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

            // AUMENTANDO O TIMEOUT PARA 30 SEGUNDOS
            $response = Http::asForm()->timeout(30)->withHeaders([
                'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
            ])->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }
            
            Log::error('Spotify API: Falha ao obter token de acesso.', ['response_body' => $response->body()]);
            return null;
        });
    }

    public function contactPage()
    {
        return view('admin.forms.contactPage');
    }
}