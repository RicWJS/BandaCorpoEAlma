{{-- laravel/resources/views/includes/footer.blade.php --}}
<footer class="main-footer section-top-border">
    <div class="footer-wrap">
        <div class="footer-container">
            <div class="footer-content">
                
                <div class="footer-block logo-block">
                    <div class="footer-logo">
                        <a href="{{ route('home') }}">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="footer-logo-img">
                        </a>
                    </div>
                </div>

                <div class="footer-column-group">
                    <div class="footer-block menu-block">
                        <h3 class="footer-title">Menu</h3>
                        <nav class="footer-nav">
                            <ul class="footer-menu">
                                <li class="footer-nav-item"><a href="{{ route('home') }}" class="footer-nav-link">Home</a></li>
                                <li class="footer-nav-item"><a href="{{ route('news.index') }}" class="footer-nav-link">Notícias</a></li>
                                <li class="footer-nav-item"><a href="{{ route('contact') }}" class="footer-nav-link">Contato</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
                
                <div class="footer-block menu-block">
                    <h3 class="footer-title">Editorias</h3>
                    <nav class="footer-nav">
                        <ul class="footer-menu">
                            {{-- MODIFICADO: A variável agora é '$categories' para manter o padrão --}}
                            @isset($categories)
                                @forelse($categories as $category)
                                <li class="footer-nav-item">
                                    <a href="{{ route('news.category', $category->slug) }}" class="footer-nav-link">{{ $category->name }}</a>
                                </li>
                                @empty
                                <li class="footer-nav-item">
                                    <span class="footer-nav-link">Nenhuma editoria.</span>
                                </li>
                                @endforelse
                            @endisset
                        </ul>
                    </nav>
                </div>

                <div class="footer-block social-block">
                    <h3 class="footer-title">Redes Sociais</h3>
                    <div class="footer-social">
                        <ul class="footer-social-list">
                            <li class="footer-social-item"><a href="{{ route('social.youtube') }}" target="_blank" class="footer-social-link"><i class="fab fa-youtube"></i><span>YouTube</span></a></li>
                            <li class="footer-social-item"><a href="{{ route('social.spotify') }}" target="_blank" class="footer-social-link"><i class="fab fa-spotify"></i><span>Spotify</span></a></li>
                            <li class="footer-social-item"><a href="{{ route('social.instagram') }}" target="_blank" class="footer-social-link"><i class="fab fa-instagram"></i><span>Instagram</span></a></li>
                            <li class="footer-social-item"><a href="{{ route('social.facebook') }}" target="_blank" class="footer-social-link"><i class="fab fa-facebook"></i><span>Facebook</span></a></li>
                            <li class="footer-social-item"><a href="{{ route('social.tiktok') }}" target="_blank" class="footer-social-link"><i class="fab fa-tiktok"></i><span>TikTok</span></a></li>
                        </ul>
                    </div>
                </div>

                <div class="footer-column-group">
                    <div class="footer-block menu-block">
                        <h3 class="footer-title">Contratantes</h3>
                        <nav class="footer-nav">
                            <ul class="footer-menu">
                                <li class="footer-nav-item">
                                    <a href="https://wa.me/5555996467227" target="_blank" class="footer-nav-link">
                                        <i class="fab fa-whatsapp"></i>Contrate o Corpo e Alma | André
                                    </a>
                                </li>
                                <li class="footer-nav-item">
                                    <a href="https://drive.google.com/drive/folders/1bbhFZNupP5cNoiGVWJZO6uEDtOljr8H6?usp=sharing" target="_blank" class="footer-nav-link">
                                        <i class="fas fa-arrow-up-right-from-square"></i>Baixe o Press Kit
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer-copyright-bar">
        <div class="footer-copyright-container">
            <p class="copyright-text">
                <i class="far fa-copyright"></i>Banda Corpo e Alma - Todos os direitos reservados.
            </p>
            <div class="developer-credit">
                Desenvolvido por: <a href="https://wa.me/5555997254389" target="_blank"><i class="fab fa-whatsapp"></i>Ricardo R.</a>
            </div>
        </div>
    </div>
</footer>
