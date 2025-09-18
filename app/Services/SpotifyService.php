<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpotifyService
{
    /**
     * Obtém a imagem da capa do Spotify em alta resolução a partir do código de embed.
     * Retorna a URL da imagem ou null em caso de falha.
     */
    public function getCoverUrlFromEmbed(?string $embedCode): ?string
    {
        if (empty($embedCode)) {
            return null;
        }

        preg_match('/\/track\/([a-zA-Z0-9]+)/', $embedCode, $matches);
        $trackId = $matches[1] ?? null;

        if (!$trackId) {
            Log::error('Spotify Service: Não foi possível extrair o Track ID.', ['embed' => $embedCode]);
            return null;
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            Log::error('Spotify Service: Falha ao obter o Access Token.');
            return null;
        }

        $response = Http::withToken($accessToken)->get("https://api.spotify.com/v1/tracks/{$trackId}");

        if ($response->successful()) {
            $trackData = $response->json();
            if (!empty($trackData['album']['images'][0]['url'])) {
                return $trackData['album']['images'][0]['url'];
            }
        }

        Log::error('Spotify Service: Falha na requisição dos dados da música.', ['status' => $response->status(), 'body' => $response->body()]);
        return null;
    }

    /**
     * Obtém um token de acesso da API do Spotify, utilizando o cache.
     */
    private function getAccessToken(): ?string
    {
        return Cache::remember('spotify_access_token', 3500, function () {
            $clientId = config('services.spotify.client_id');
            $clientSecret = config('services.spotify.client_secret');

            if (!$clientId || !$clientSecret) {
                Log::error('Credenciais da API do Spotify não configuradas.');
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
}