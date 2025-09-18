@extends('admin.layouts.app')

@section('title', 'Editar Seção Spotify')

@section('content')
<div class="admin-form-container">
    <div class="form-header">
        <h1>Gerenciar Seção Spotify</h1>
        <p>Controle o conteúdo da seção "Ouça no Spotify" que aparece na página inicial.</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-error">
            <strong>Oops! Verifique os erros abaixo:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.forms.spotifySection.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="title">Título da Seção</label>
            <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $spotifySection->title ?? 'Ouça Agora no Spotify') }}" required>
        </div>

        <div class="form-group">
            <label for="embed_link">Link da Música do Spotify</label>
            <input type="url" id="embed_link" name="embed_link" class="form-control" value="{{ old('embed_link', $spotifySection->embed_link ?? '') }}" placeholder="https://open.spotify.com/track/..." required>
            <p class="form-text">Cole aqui o link do artista do Spotify. A capa do álbum será buscada automaticamente.</p>
        </div>

        @if (!empty($spotifySection->cover_image_url))
            <div class="form-group">
                <label>Capa do Álbum Atual (Automática)</label>
                <div>
                    <img src="{{ $spotifySection->cover_image_url }}" alt="Capa do Álbum" style="max-width: 200px; border-radius: 8px;">
                </div>
            </div>
        @endif
        
        <div class="form-footer">
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </div>
    </form>
</div>
@endsection