{{-- laravel\resources\views\admin\forms\bannerSection.blade.php --}}
@extends('admin.layouts.app')

@section('content')
    <div class="admin-form-container">
        <div class="form-header">
            <h1>Gerenciar Banner Principal</h1>
            <p>Atualize as informações do banner que aparece na página inicial do site.</p>
        </div>

        {{-- Exibe mensagem de sucesso, se houver --}}
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        {{-- Exibe erros de validação, se houverem --}}
        @if ($errors->any())
            <div class="alert alert-error" role="alert">
                <strong>Oops! Houve alguns problemas:</strong>
                <ul style="list-style-position: inside; padding-left: 10px; margin-top: 8px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.forms.bannerSection.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="title">Título</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $banner->title ?? '') }}" placeholder="Ex: Novo Single Disponível Agora" required>
            </div>

            <div class="form-group">
                <label for="excerpt">Texto de Introdução</label>
                <textarea id="excerpt" name="excerpt" class="form-control" rows="4" placeholder="Descreva brevemente a novidade ou chamada para ação...">{{ old('excerpt', $banner->excerpt ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label for="image">Imagem do Banner</label>
                <div class="file-input-wrapper">
                    <input type="file" id="image" name="image" class="custom-file-input">
                    <label for="image" class="file-input-label">
                        <span class="file-input-icon">🖼️</span>
                        <span class="file-input-text">Clique para escolher uma nova imagem...</span>
                    </label>
                </div>
                <p class="form-text">Recomendação: Imagens no formato paisagem (1920x1080px) para melhor visualização.</p>

                {{-- Preview da Imagem Atual --}}
                @if ($banner && $banner->image_path)
                    <div class="image-preview">
                        <p class="preview-label">Imagem Atual:</p>
                        <img src="{{ asset('storage/' . $banner->image_path) }}" alt="Banner Atual">
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label for="youtube_link">Link do YouTube</label>
                <input type="url" id="youtube_link" name="youtube_link" class="form-control" value="{{ old('youtube_link', $banner->youtube_link ?? '') }}" placeholder="https://www.youtube.com/watch?v=...">
                <p class="form-text">Opcional.</p>
            </div>

            <div class="form-group">
                <label for="spotify_link">Link do Spotify</label>
                <input type="url" id="spotify_link" name="spotify_link" class="form-control" value="{{ old('spotify_link', $banner->spotify_link ?? '') }}" placeholder="https://open.spotify.com/track/...">
                <p class="form-text">Opcional.</p>
            </div>

            {{-- NOVO CAMPO: Link Saiba Mais --}}
            <div class="form-group">
                <label for="learn_more_link">Link "Saiba Mais"</label>
                <input type="url" id="learn_more_link" name="learn_more_link" class="form-control" value="{{ old('learn_more_link', $banner->learn_more_link ?? '') }}" placeholder="http://seusite.com/saiba-mais...">
                <p class="form-text">Opcional: Um link para uma página com mais detalhes.</p>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
@endsection
