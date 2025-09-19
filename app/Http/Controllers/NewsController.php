<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session; // ADICIONADO

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

    // MODIFICADO: Método 'show' agora aceita Request e verifica o modo preview
    public function show(Request $request, $slug)
    {
        $query = Post::with('category')->where('slug', $slug);

        // Se o parâmetro 'preview' existir e o admin estiver logado,
        // a verificação de status é ignorada, permitindo ver rascunhos.
        if (!$request->has('preview') || !Session::has('admin_authenticated')) {
            $query->where('status', 'published');
        }

        $post = $query->firstOrFail();

        // O contador de visualizações só incrementa se o post estiver
        // publicado e não estiver em modo de pré-visualização.
        if ($post->status == 'published' && !$request->has('preview')) {
            $post->increment('views_count');
        }

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
            'currentCategory' => $currentCategory,
            'showRecentNewsWidget' => false,
            'searchTerm' => $searchTerm,
        ]);
    }
}