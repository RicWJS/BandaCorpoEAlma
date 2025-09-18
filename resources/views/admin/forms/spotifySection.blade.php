@extends('admin.layouts.app')

@section('title', 'Editar Seção Spotify')

@section('content')
<div class="container">
    <h1>Editar Seção Spotify da Home</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.forms.spotifySection.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="title">Título da Seção</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $spotifySection->title ?? '') }}" required>
        </div>

        <div class="form-group">
            <label for="embed_code">Código de Incorporação do Spotify (iframe)</label>
            <textarea name="embed_code" id="embed_code" class="form-control" rows="4" required>{{ old('embed_code', $spotifySection->embed_code ?? '') }}</textarea>
            <small>Cole o código completo fornecido pelo Spotify, começando com `&lt;iframe...`.</small>
        </div>

        {{-- Exibição da capa do álbum atual --}}
        @if (!empty($spotifySection->cover_image_url))
            <div class="form-group">
                <label>Capa do Álbum Atual</label>
                <div>
                    <img src="{{ $spotifySection->cover_image_url }}" alt="Capa do Álbum" style="max-width: 200px; border-radius: 8px;">
                </div>
                <small>A capa é obtida automaticamente ao salvar.</small>
            </div>
        @endif


        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    </form>
</div>
@endsection