{{-- laravel\resources\views\admin\layouts\app.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin')</title>

    <link rel="icon" href="icon.jpeg" type="image/x-icon">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <link rel="stylesheet" type="text/css" href="{{ asset('css/admin/layout.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/admin/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/admin/sidebar.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/admin/forms.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/admin/posts.css') }}">
</head>
<body class="admin-body">
    <div class="admin-container">
        {{-- Inclui o menu de navegação lateral --}}
        @include('admin.partials._sidebar')

        {{-- Área de conteúdo principal --}}
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.tiny.cloud/1/7gmgog2gg35jmrbda1par3c76jgwpplpu16gebwk09ml54an/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="{{ asset('js/admin.js') }}" defer></script>
    
    @stack('scripts')
</body>
</html>
