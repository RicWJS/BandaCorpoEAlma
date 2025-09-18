{{-- laravel\resources\views\admin\auth\login.blade.php --}}
@extends('admin.layouts.guest')

@section('title', 'Login - Admin')

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h1>Admin</h1>
            <p>Faça login para acessar o painel administrativo</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->has('login'))
            <div class="alert alert-error">
                {{ $errors->first('login') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}" class="login-form">
            @csrf
            
            <div class="form-group">
                <label for="username">Usuário</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="{{ old('username') }}"
                    class="form-control @error('username') error @enderror"
                    required
                    autocomplete="username"
                    autofocus
                >
                @error('username')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control @error('password') error @enderror"
                    required
                    autocomplete="current-password"
                >
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-login">
                Entrar
            </button>
        </form>

        <div class="login-footer">
            <small>&copy; {{ date('Y') }} CMS Admin. Todos os direitos reservados.</small>
        </div>
    </div>
</div>
@endsection
