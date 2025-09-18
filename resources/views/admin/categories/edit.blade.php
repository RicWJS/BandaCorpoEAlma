@extends('admin.layouts.app')

@section('title', 'Editar Categoria - Admin')

@section('content')
<div class="admin-form-container">
    <div class="form-header">
        <h1>Editar Categoria</h1>
        <p>Altere o campo abaixo para atualizar a categoria.</p>
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

    @include('admin.categories.partials._form', ['category' => $category])
</div>
@endsection
