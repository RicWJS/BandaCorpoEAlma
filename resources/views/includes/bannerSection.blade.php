{{-- resources/views/includes/bannerSection.blade.php --}}
<section class="main-banner-section" style="background-image: url('{{ asset('storage/' . $bannerData->image_path) }}');">
    <div class="section-overlay"></div>
    <div class="section-container">
        <div class="main-banner-content">
            <div class="main-banner-content-container">
                <h1 class="main-banner-title">{{ $bannerData->title }}</h1>
                <div class="main-banner-excerpt">
                    <p>{!! $bannerData->excerpt !!}</p>
                </div>

                <div class="main-banner-buttons">
                    @if ($bannerData->spotify_link)
                        <a href="{{ $bannerData->spotify_link }}" target="_blank" class="btn-spotify">
                            <i class="fab fa-spotify"></i> Ouça no Spotify
                        </a>
                    @endif

                    @if ($bannerData->learn_more_link)
                        <a href="{{ $bannerData->learn_more_link }}" class="btn-spotify btn-learn-more" target="_blank">
                            Saiba Mais <i class="fas fa-arrow-right"></i>
                        </a>
                    @endif

                    {{-- Botão alterado para <button> e com data-attribute para o link --}}
                    @if ($bannerData->youtube_link)
                        <button class="btn-video-popup" data-youtube-link="{{ $bannerData->youtube_link }}">
                            <div class="popup-play-icon">
                                <div class="popup-play-icon-border"></div>
                                <i class="fa-solid fa-play"></i>
                            </div>
                            <span>Assista ao clipe</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Estrutura do Modal/Popup do Vídeo (fica oculta por padrão) --}}
@if ($bannerData->youtube_link)
<div class="video-popup-overlay" id="video-popup-overlay">
    <div class="video-popup-container">
        <div class="video-popup-content">
            <iframe id="youtube-video-iframe" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
        <button class="video-popup-close" id="video-popup-close">&times;</button>
    </div>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const videoPopupOverlay = document.querySelector('.video-popup-overlay');
        const videoPopupCloseButton = document.querySelector('.video-popup-close');
        const videoPopupContent = document.querySelector('.video-popup-content');
        const btnVideoPopups = document.querySelectorAll('.btn-video-popup');

        btnVideoPopups.forEach(button => {
            button.addEventListener('click', function() {
                const youtubeLink = this.dataset.youtubeLink;
                if (youtubeLink) {
                    const videoId = getYouTubeVideoId(youtubeLink);
                    if (videoId) {
                        videoPopupContent.innerHTML = `<iframe src="https://www.youtube.com/embed/${videoId}?autoplay=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
                        videoPopupOverlay.classList.add('active');
                    }
                }
            });
        });

        videoPopupCloseButton.addEventListener('click', function() {
            closeVideoPopup();
        });

        videoPopupOverlay.addEventListener('click', function(event) {
            if (event.target === videoPopupOverlay) {
                closeVideoPopup();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && videoPopupOverlay.classList.contains('active')) {
                closeVideoPopup();
            }
        });

        function closeVideoPopup() {
            videoPopupOverlay.classList.remove('active');
            videoPopupContent.innerHTML = ''; // Stop video playback
        }

        function getYouTubeVideoId(url) {
            const regExp = /(?:https?:\/\/)?(?:www\.)?(?:m\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=|embed\/|v\/|)([\w-]{11})(?:\S+)?/;
            const match = url.match(regExp);
            return (match && match[1]) ? match[1] : null;
        }
    });
</script>
