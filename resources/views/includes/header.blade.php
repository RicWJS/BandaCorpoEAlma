{{-- laravel\resources\views\includes\header.blade.php --}}
<header class="main-header {{ Route::currentRouteName() === 'home' ? 'header-absolute' : '' }}">
    <div class="container">
        <div class="header-content">
            <button class="nav-toggle" id="navToggle" aria-label="Abrir menu">
                <i class="fas fa-bars"></i>
            </button>

            <div class="logo">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-img">
                </a>
            </div>

            <nav class="main-nav" id="mainNav">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }} interactive-element">
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('news.index') }}" class="nav-link {{ request()->routeIs('news.index') ? 'active' : '' }} interactive-element">
                            Notícias
                        </a>
                    </li>
                </ul>

                <div class="social-icons">
                    <ul class="social-icons-list">
                        <li class="social-item youtube-item">
                            <a href="{{ route('social.youtube') }}" target="_blank" class="social-link interactive-element" title="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </li>
                        <li class="social-item spotify-item">
                            <a href="{{ route('social.spotify') }}" target="_blank" class="social-link interactive-element" title="Spotify">
                                <i class="fab fa-spotify"></i>
                            </a>
                        </li>
                        <li class="social-item instagram-item">
                            <a href="{{ route('social.instagram') }}" target="_blank" class="social-link interactive-element" title="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </li>
                        <li class="social-item facebook-item">
                            <a href="{{ route('social.facebook') }}" target="_blank" class="social-link interactive-element" title="Facebook">
                                <i class="fab fa-facebook"></i>
                            </a>
                        </li>
                        <li class="social-item tiktok-item">
                            <a href="{{ route('social.tiktok') }}" target="_blank" class="social-link interactive-element" title="TikTok">
                                <i class="fab fa-tiktok"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</header>
