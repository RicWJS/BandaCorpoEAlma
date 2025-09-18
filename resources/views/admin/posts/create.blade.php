@extends('admin.layouts.app')

@section('title', 'Criar Novo Post - Admin')

@section('content')
<div class="admin-form-container">
    <div class="form-header">
        <h1>Criar Nova Notícia</h1>
        <p>Preencha os campos abaixo para adicionar um novo post ao site.</p>
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

    {{-- Inclui o formulário parcial. Nenhum dado é passado, então ele age como 'criação' --}}
    @include('admin.posts.partials._form')
</div>
@endsection
