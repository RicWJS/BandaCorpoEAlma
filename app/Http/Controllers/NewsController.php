<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->input('s');

        $query = Post::with('category')->where('status', 'published');

        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('content', 'like', '%' . $searchTerm . '%');
            });
        }

        $postsPerPage = 2;
        $posts = $query->latest('published_at')->paginate($postsPerPage)->withQueryString();
        
        return view('news', [
            'posts' => $posts,
            'showRecentNewsWidget' => false,
            'searchTerm' => $searchTerm
        ]);
    }

    public function show($slug)
    {
        $post = Post::with('category')->where('slug', $slug)->where('status', 'published')->firstOrFail();
        $post->increment('views_count');

        $recentNews = Post::where('status', 'published')
                          ->where('id', '!=', $post->id)
                          ->whereNotNull('published_at')
                          ->orderBy('published_at', 'desc')
                          ->take(3)
                          ->get();

        return view('news-detail', compact('post', 'recentNews'));
    }

    public function showByCategory(Request $request, $slug)
    {
        $currentCategory = Category::where('slug', $slug)->firstOrFail();
        $searchTerm = $request->input('s');
        $query = $currentCategory->posts()
                                 ->with('category')
                                 ->where('status', 'published');
    
        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('content', 'like', '%' . $searchTerm . '%');
            });
        }
    
        $posts = $query->latest('published_at')->paginate(10)->withQueryString();
    
        return view('news.category', [
            'posts' => $posts,
            // 'categories' => $categories, // A linha foi removida
            'currentCategory' => $currentCategory,
            'showRecentNewsWidget' => false,
            'searchTerm' => $searchTerm,
        ]);
    }
}