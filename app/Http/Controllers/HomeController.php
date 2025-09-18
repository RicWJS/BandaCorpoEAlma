<?php
// app/Http/Controllers/HomeController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\BannerSection;
use App\Models\SpotifySection;
use App\Models\Post; // <-- Teríamos que importar o Model

class HomeController extends Controller
{
    public function index()
    {
        $bannerData = BannerSection::first();
        $spotifyData = SpotifySection::first();
        $recentNews = Post::where('status', 'published')
                          ->whereNotNull('published_at')
                          ->orderBy('published_at', 'desc')
                          ->take(3)
                          ->get();

        return view('home', [
            'bannerData' => $bannerData,
            'spotifyData' => $spotifyData,
            'recentNews' => $recentNews
        ]);
    }
}
