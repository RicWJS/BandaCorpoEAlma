{{-- resources/views/includes/spotifySection.blade.php --}}

@if ($spotifyData && ($spotifyData->cover_image_url || $spotifyData->embed_link))
    <section class="spotify-embed-section section-top-border repeat-bg" style="background-image: url('{{ asset('images/logo-pattern.png') }}')">
        <div class="section-wrap">
            <div class="section-container">
                <div class="section-title">
                    <h2><span>Ouça Agora no</span> Spotify</h2>
                </div>
                
                <div class="section-content">
                    {{-- Coluna da Imagem (buscando da nova URL da API) --}}
                    @if ($spotifyData->cover_image_url)
                        <div class="spotify-cover">
                            <img src="{{ $spotifyData->cover_image_url }}" alt="Capa do Artista ou Álbum no Spotify">
                        </div>
                    @endif

                    {{-- Coluna do Player (usando o novo embed_link) --}}
                    @if ($spotifyData->embed_link)
                        <div class="spotify-embed">
                            @php
                                // Transforma o link normal do Spotify em um link de embed
                                $embedUrl = preg_replace(
                                    '/(https:\/\/open\.spotify\.com)\/(track|artist|album|playlist)\/([a-zA-Z0-9]+)/',
                                    '$1/embed/$2/$3',
                                    $spotifyData->embed_link
                                );
                            @endphp
                            <iframe 
                                style="border-radius:12px" 
                                src="{{ $embedUrl }}" 
                                width="100%" 
                                frameBorder="0" 
                                allowfullscreen="" 
                                allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" 
                                loading="lazy">
                            </iframe>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif