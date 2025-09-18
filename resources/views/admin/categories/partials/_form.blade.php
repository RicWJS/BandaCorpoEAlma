<form action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST">
    @csrf
    @if (isset($category))
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="name">Nome da Categoria</label>
        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $category->name ?? '') }}" required>
    </div>

    <div class="form-footer">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">
            {{ isset($category) ? 'Atualizar Categoria' : 'Salvar Categoria' }}
        </button>
    </div>
</form>
