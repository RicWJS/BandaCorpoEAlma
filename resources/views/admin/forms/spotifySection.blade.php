{{-- laravel\resources\views\admin\forms\spotifySection.blade.php --}}
@extends('admin.layouts.app')

@section('content')
    <div class="admin-form-container">
        <div class="form-header">
            <h1>Gerenciar Seção Spotify</h1>
            <p>Controle a visibilidade e o conteúdo da seção "Ouça no Spotify" na home.</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.forms.spotifySection.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>Exibir seção no site?</label>
                <label class="toggle-switch">
                    <input type="checkbox" name="is_visible" value="1" {{ old('is_visible', $spotify->is_visible ?? true) ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
                <p class="form-text">Se desativado, a seção inteira não aparecerá no site.</p>
            </div>

            <div class="form-group">
                <label for="cover_image">Imagem da Capa</label>
                <div class="file-input-wrapper">
                    <input type="file" id="cover_image" name="cover_image" class="custom-file-input">
                    <label for="cover_image" class="file-input-label">
                        <span class="file-input-icon">🖼️</span>
                        <span class="file-input-text">Clique para escolher uma imagem...</span>
                    </label>
                </div>
                <p class="form-text">Recomendação: Imagens no formato quadrado (720x720px) para melhor visualização.</p>

                @if ($spotify && $spotify->cover_image_path)
                    <div class="image-preview">
                        <p class="preview-label">Capa Atual:</p>
                        <img src="{{ asset('storage/' . $spotify->cover_image_path) }}" alt="Capa Atual">
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label for="spotify_link">Link de Incorporação (Embed) do Spotify</label>
                <input type="url" id="spotify_link" name="spotify_link" class="form-control" value="{{ old('spotify_link', $spotify->spotify_link ?? '') }}" placeholder="Cole o link 'src' do iframe do Spotify aqui">
                <p class="form-text">Ex: https://open.spotify.com/embed/artist/xxxxxxxxxxxxxxxxxxx?utm_source=generator&theme=0</p>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
@endsection
