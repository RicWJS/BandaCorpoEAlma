{{-- laravel\resources\views\admin\categories\index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Todas as Categorias - Admin')

@section('content')
<div class="admin-table-container">
    <div class="table-header">
        <h1>Todas as Categorias</h1>
        <p>Gerencie as categorias de notícias do site.</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <table class="admin-table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Slug</th>
                <th>Qtd. Posts</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td class="post-title">{{ $category->name }}</td>
                    <td>{{ $category->slug }}</td>
                    <td>{{ $category->posts_count }}</td>
                    <td class="actions">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn-action btn-edit" title="Editar">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action btn-delete" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta categoria?');">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Nenhuma categoria encontrada. <a href="{{ route('admin.categories.create') }}">Crie a primeira!</a></td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-container">
        {{ $categories->links() }}
    </div>
</div>
@endsection
