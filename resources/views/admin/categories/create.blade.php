@extends('admin.layouts.app')

@section('title', 'Adicionar Categoria - Admin')

@section('content')
<div class="admin-form-container">
    <div class="form-header">
        <h1>Adicionar Nova Categoria</h1>
        <p>Preencha o campo abaixo para criar uma nova categoria.</p>
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

    @include('admin.categories.partials._form')
</div>
@endsection