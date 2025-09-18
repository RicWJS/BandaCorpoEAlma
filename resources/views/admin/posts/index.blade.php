{{-- resources/views/admin/posts/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Todos os Posts - Admin')

@section('content')
<div class="admin-table-container">
    <div class="table-header">
        <h1>Todas as Notícias</h1>
        <p>Gerencie, edite ou exclua as notícias do site.</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="admin-table">
        <thead>
            <tr>
                <th>Imagem</th>
                <th>Título</th>
                <th>Categoria</th>
                <th>Status</th>
                <th>Visualizações</th> 
                <th>Data de Publicação</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($posts as $post)
                <tr>
                    <td>
                        @if ($post->thumbnail_path)
                            <img src="{{ asset('storage/' . $post->thumbnail_path) }}" alt="Thumbnail" class="table-thumbnail">
                        @else
                            <div class="table-thumbnail-placeholder">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                    </td>
                    <td class="post-title">{{ $post->title }}</td>
                    <td>{{ $post->category->name ?? 'N/A' }}</td> 
                    <td>
                        <span class="status-badge status-{{ $post->status }}">
                            {{ $post->status_text }}
                        </span>
                    </td>
                    <td>{{ $post->views_count }}</td> 
                    <td>
                        {{ $post->published_at ? $post->published_at->format('d/m/Y H:i') : 'Não publicado' }}
                    </td>
                    <td class="actions">
                        
                        {{-- =========== CÓDIGO ADICIONADO =========== --}}
                        @if ($post->status == 'published')
                            <a href="{{ route('news.show', $post->slug) }}" target="_blank" class="btn-action btn-view" title="Visualizar no site">
                                <i class="fas fa-eye"></i>
                            </a>
                        @endif
                        {{-- ========================================= --}}

                        <a href="{{ route('admin.posts.edit', $post) }}" class="btn-action btn-edit" title="Editar">
                            <i class="fas fa-pencil-alt"></i>
                        </a>

                        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action btn-delete" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este post? Esta ação não pode ser desfeita.');">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Nenhum post encontrado. <a href="{{ route('admin.posts.create') }}">Crie o primeiro!</a></td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Links de Paginação --}}
    <div class="pagination-container">
        {{ $posts->links() }}
    </div>
</div>
@endsection