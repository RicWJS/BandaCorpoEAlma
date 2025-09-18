{{-- laravel\resources\views\admin\posts\partials\_form.blade.php --}}
<form action="{{ isset($post) ? route('admin.posts.update', $post) : route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    {{-- Se estivermos editando, adicionamos o método PUT --}}
    @if (isset($post))
        @method('PUT')
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="form-group">
        <label>Publicar Post?</label>
        <label class="toggle-switch">
            {{-- Este input oculto garante que, se o switch estiver desligado, o valor 'draft' seja enviado. --}}
            <input type="hidden" name="status" value="draft">
            {{-- O valor 'published' só é enviado se o switch estiver ligado, sobrescrevendo o 'draft'. --}}
            <input type="checkbox" name="status" value="published" {{ old('status', $post->status ?? 'published') == 'published' ? 'checked' : '' }}>            <span class="slider"></span>
        </label>
        <p class="form-text">Se ativado, o post ficará visível publicamente no site.</p>
    </div>

    <div class="form-group">
        <label for="title">Título do Post</label>
        {{-- O ?? '' garante que o campo fique vazio no formulário de criação --}}
        <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $post->title ?? '') }}" required>
    </div>

    <div class="form-group">
        <label for="category_id">Categoria</label>
        <select id="category_id" name="category_id" class="form-control">
            <option value="">Nenhuma</option>
            @if(isset($categories))
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $post->category_id ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            @endif
        </select>
        <p class="form-text">Associe este post a uma categoria.</p>
    </div>

    <div class="form-group">
        <label for="excerpt">Resumo (Chamada)</label>
        <textarea id="excerpt" name="excerpt" class="form-control" rows="3">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
        <p class="form-text">Este texto aparecerá na listagem de notícias.</p>
    </div>

    <div class="form-group">
        <label for="content">Conteúdo Completo</label>
        <textarea id="content" name="content" class="form-control" rows="15">{{ old('content', $post->content ?? '') }}</textarea>
    </div>

    <div class="form-group">
        <label for="image">Imagem de Destaque</label>
        <div class="file-input-wrapper">
            <input type="file" id="image" name="image" class="custom-file-input">
            <label for="image" class="file-input-label">
                <span class="file-input-icon">🖼️</span>
                <span class="file-input-text">Clique para escolher uma imagem...</span>
            </label>
        </div>
        @if (isset($post) && $post->thumbnail_path)
            <div class="image-preview">
                <p class="preview-label">Imagem Atual:</p>
                <img src="{{ asset('storage/' . $post->thumbnail_path) }}" alt="Thumbnail Atual">
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="published_at">Data de Publicação (Opcional)</label>
        <input type="datetime-local" id="published_at" name="published_at" class="form-control" value="{{ old('published_at', isset($post) && $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}">
        <p class="form-text">Deixe em branco para preencher automaticamente ao salvar.</p>
    </div>

    <div class="form-footer">
        <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Cancelar</a>
        {{-- O texto do botão muda dependendo do contexto --}}
        <button type="submit" class="btn btn-primary">
            {{ isset($post) ? 'Atualizar Post' : 'Salvar Post' }}
        </button>
    </div>
</form>

{{-- O script do TinyMCE permanece o mesmo. --}}
@push('scripts')
<script>
    tinymce.init({
        selector: 'textarea#content',
        // ... (resto das configurações do TinyMCE sem alterações)
        plugins: 'code table lists link image media',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table | link image media',
        skin: 'oxide-dark',
        content_css: 'dark',
        height: 500,
        language: 'pt_BR',
        content_style: `
            body { background-color: #22262e; color: #ffffff; font-family: Arial, sans-serif; font-size: 14px; line-height: 1.4; margin: 10px; }
            p { margin: 0 0 10px 0; }
            h1, h2, h3, h4, h5, h6 { color: #ffffff; margin-top: 20px; margin-bottom: 10px; }
            a { color: #4a9eff; }
            code { background-color: #3f4550; padding: 2px 4px; border-radius: 3px; color: #ffffff; }
            pre { background-color: #3f4550; padding: 10px; border-radius: 4px; color: #ffffff; }
            table { border-collapse: collapse; width: 100%; }
            table td, table th { border: 1px solid #3f4550; padding: 8px; color: #ffffff; }
            table th { background-color: #3f4550; }
        `,
        setup: function(editor) {
            editor.on('init', function() {
                editor.getBody().style.backgroundColor = '#22262e';
            });
        }
    });
</script>
@endpush