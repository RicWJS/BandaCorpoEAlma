<?php
// app/Http/Controllers/Admin/PostController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category; // ADICIONADO
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    use ImageUploadTrait;

    public function index()
    {
        // MODIFICADO: Adicionado eager loading da categoria e aumentado para 15.
        $posts = Post::with('category')->latest()->paginate(15);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        // ADICIONADO: Busca todas as categorias para o formulário.
        $categories = Category::orderBy('name')->get();
        return view('admin.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // MODIFICADO: Adicionada validação para category_id
        $validatedData = $request->validate([
            'title' => 'required|string|max:255|unique:posts,title',
            'category_id' => 'nullable|exists:categories,id', // ADICIONADO
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|max:32000',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
        ]);

        // MODIFICADO: Usando $validatedData para garantir que apenas dados validados sejam usados
        $data = $validatedData;
        $data['slug'] = Str::slug($validatedData['title']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $this->handleImageUpload($request, 'image', 'posts', 1080);
            $data['thumbnail_path'] = $this->handleImageUpload($request, 'image', 'posts/thumbnails', 240);
        }
        
        if ($data['status'] == 'published' && !$request->published_at) {
            $data['published_at'] = now();
        }

        $post = Post::create($data);

        return redirect()->route('admin.posts.edit', $post)->with('success', 'Post criado com sucesso!');
    }

    public function edit(Post $post)
    {
        // ADICIONADO: Busca todas as categorias para o formulário de edição.
        $categories = Category::orderBy('name')->get();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        // MODIFICADO: Adicionada validação para category_id
        $validatedData = $request->validate([
            'title' => ['required', 'string', 'max:255', Rule::unique('posts')->ignore($post->id)],
            'category_id' => 'nullable|exists:categories,id', // ADICIONADO
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|max:32000',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
        ]);
        
        // MODIFICADO: Usando $validatedData para garantir que apenas dados validados sejam atualizados
        $data = $validatedData;
        $data['slug'] = Str::slug($validatedData['title']);

        if ($request->hasFile('image')) {
            if ($post->image_path && Storage::disk('public')->exists($post->image_path)) {
                Storage::disk('public')->delete($post->image_path);
            }
            if ($post->thumbnail_path && Storage::disk('public')->exists($post->thumbnail_path)) {
                Storage::disk('public')->delete($post->thumbnail_path);
            }
            $data['image_path'] = $this->handleImageUpload($request, 'image', 'posts', 1920);
            $data['thumbnail_path'] = $this->handleImageUpload($request, 'image', 'posts/thumbnails', 512);
        }
        
        if ($data['status'] == 'published' && is_null($post->published_at)) {
            $data['published_at'] = now();
        } elseif ($data['status'] == 'draft') {
            // Opcional: se voltar para rascunho, pode-se querer limpar a data de publicação
            // $data['published_at'] = null; 
        }

        $post->update($data);

        return redirect()->route('admin.posts.edit', $post)->with('success', 'Post atualizado com sucesso!');
    }

    public function destroy(Post $post)
    {
        if ($post->image_path && Storage::disk('public')->exists($post->image_path)) {
            Storage::disk('public')->delete($post->image_path);
        }
        if ($post->thumbnail_path && Storage::disk('public')->exists($post->thumbnail_path)) {
            Storage::disk('public')->delete($post->thumbnail_path);
        }

        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Post excluído com sucesso!');
    }
}
