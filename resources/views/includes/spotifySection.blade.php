{{-- resources/views/includes/spotifySection.blade.php --}}

@if ($spotifyData && $spotifyData->is_visible)
    <section class="spotify-embed-section section-top-border repeat-bg" style="background-image: url('{{ asset('images/logo-pattern.png') }}')">
        <div class="section-wrap">
            <div class="section-container">
                <div class="section-title">
                    <h2><span>Ouça Agora no</span> Spotify</h2>
                </div>
                
                <div class="section-content">
                    <div class="spotify-cover">
                        <img src="storage/{{ $spotifyData->cover_image_path }}" alt="Capa do Single/Álbum">
                    </div>
                    <div class="spotify-embed">
                        {{-- Iframe dinâmico, só mostra se o link existir --}}
                        @if ($spotifyData->spotify_link)
                            <iframe style="border-radius:12px" src="{{ $spotifyData->spotify_link }}" width="100%" height="352" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                        @else
                            <p>Em breve, novidades no Spotify!</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
