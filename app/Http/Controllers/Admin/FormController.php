<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Traits\ImageUploadTrait;

use App\Models\BannerSection;
use App\Models\SpotifySection;

class FormController extends Controller
{
    use ImageUploadTrait;

    public function bannerSection()
    {
        $banner = BannerSection::first();
        return view('admin.forms.bannerSection', compact('banner'));
    }

    public function storeBannerSection(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'image' => 'nullable|image|max:32000',
            'youtube_link' => 'nullable|url',
            'spotify_link' => 'nullable|url',
            'learn_more_link' => 'nullable|url', // Adicione esta linha para validação
        ]);

        $banner = BannerSection::firstOrNew(['id' => 1]);

        if ($request->hasFile('image')) {
            if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
                Storage::disk('public')->delete($banner->image_path);
            }

            // Passa a largura máxima de 1080px
            $imagePath = $this->handleImageUpload($request, 'image', 'banners', 1920);
            if ($imagePath) {
                $banner->image_path = $imagePath;
            }
        }

        $banner->title = $request->title;
        $banner->excerpt = $request->excerpt;
        $banner->youtube_link = $request->youtube_link;
        $banner->spotify_link = $request->spotify_link;
        $banner->learn_more_link = $request->learn_more_link; // Adicione esta linha para salvar o link
        $banner->save();

        return redirect()->route('admin.forms.bannerSection')->with('success', 'Banner atualizado com sucesso!');
    }

    public function spotifySection()
    {
        $spotify = SpotifySection::first();
        return view('admin.forms.spotifySection', compact('spotify'));
    }

    public function storeSpotifySection(Request $request)
    {
        $request->validate([
            'cover_image' => 'nullable|image|max:32000', // 5MB
            'spotify_link' => 'nullable|url',
        ]);

        $spotifySection = SpotifySection::firstOrNew(['id' => 1]);

        if ($request->hasFile('cover_image')) {
            if ($spotifySection->cover_image_path && Storage::disk('public')->exists($spotifySection->cover_image_path)) {
                Storage::disk('public')->delete($spotifySection->cover_image_path);
            }

            // Passa a largura máxima de 720px
            $imagePath = $this->handleImageUpload($request, 'cover_image', 'spotify_covers', 720);
            if ($imagePath) {
                $spotifySection->cover_image_path = $imagePath;
            }
        }

        $spotifySection->is_visible = $request->has('is_visible');
        $spotifySection->spotify_link = $request->spotify_link;
        $spotifySection->save();

        return redirect()->route('admin.forms.spotifySection')->with('success', 'Seção Spotify atualizada com sucesso!');
    }

    public function contactPage()
    {
        return view('admin.forms.contactPage');
    }
}