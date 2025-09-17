{{-- laravel\resources\views\admin\layouts\guest.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin')</title>

    <link rel="icon" href="icon.jpeg" type="image/x-icon">
    
    <link rel="stylesheet" type="text/css" href="{{ asset('css/admin/layout.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/admin/login.css') }}">
</head>
<body class="admin-body">
    @yield('content')

    @stack('scripts')
</body>
</html>