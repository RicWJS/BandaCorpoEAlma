{{-- laravel\resources\views\layouts\app.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Meu Site')</title>

    {{-- =========== CÓDIGO ADICIONADO =========== --}}
    @stack('meta_tags')
    {{-- ========================================= --}}
    
    <link rel="icon" href="{{ asset('icon.jpeg') }}" type="image/x-icon"> {{-- Boa prática: usar o helper asset() --}}

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <link rel="stylesheet" type="text/css" href="{{ asset('css/site/base.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/site/layout.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/site/header.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/site/footer.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/site/components/main-banner-section.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/site/components/recent-news-section.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/site/components/spotify-embed-section.css') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Praise&Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">

    @stack('styles')
</head>
<body>
    <div class="body-wrap">
        @yield('content')
        
        @stack('scripts')

        <script>
            // SCRIPT PARA O MENU HAMBÚRGUER
            document.addEventListener('DOMContentLoaded', () => {
                const navToggle = document.getElementById('navToggle');
                const mainNav = document.getElementById('mainNav');

                if (navToggle && mainNav) {
                    navToggle.addEventListener('click', () => {
                        mainNav.classList.toggle('is-open');
                        const icon = navToggle.querySelector('i');
                        if (mainNav.classList.contains('is-open')) {
                            icon.classList.remove('fa-bars');
                            icon.classList.add('fa-xmark');
                        } else {
                            icon.classList.remove('fa-xmark');
                            icon.classList.add('fa-bars');
                        }
                    });
                }
            });
            
            // EFEITO EM BOTÕES AO PASSAR O MOUSE
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.interactive-element').forEach(button => {
                    button.addEventListener('mousemove', (e) => {
                        const rect = button.getBoundingClientRect();
                        const x = ((e.clientX - rect.left) / rect.width) * 100;
                        const y = ((e.clientY - rect.top) / rect.height) * 100;
                        
                        button.style.setProperty('--x', x + '%');
                        button.style.setProperty('--y', y + '%');
                    });
                });
            });
        </script>
    </div>
</body>
</html>
