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
            <label for="embed_link">Link ou Código de Embed do Spotify</label>
            <textarea id="embed_link" name="embed_link" class="form-control" rows="4" required>{{ old('embed_link', $spotifySection->embed_link ?? '') }}</textarea>
            <p class="form-text">Cole aqui o link do embed/iframe do Spotify. A capa do álbum será buscada automaticamente.</p>
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