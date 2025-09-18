@extends('admin.layouts.app')

@section('title', 'Editar Post - Admin')

@section('content')
<div class="admin-form-container">
    <div class="form-header">
        <h1>Editar Notícia</h1>
        <p>Altere os campos abaixo para atualizar o post.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-error">
            <strong>Oops!</strong> Verifique os erros abaixo.
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Inclui o mesmo formulário parcial, mas passa a variável $post --}}
    @include('admin.posts.partials._form', ['post' => $post])
</div>
@endsection
