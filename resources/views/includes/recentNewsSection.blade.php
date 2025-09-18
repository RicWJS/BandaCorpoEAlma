{{-- laravel\resources\views\includes\recentNewsSection.blade.php --}}

<section class="recent-news-section section-top-border" style="background-image: url('{{ asset('images/recent-news-bg.jpg') }}')">
    <div class="section-wrap bg-black-80">
        <div class="section-container">
            <div class="section-title">
                <h2>Notícias Recentes</h2>
            </div>

            <div class="news-grid">
                @forelse($recentNews as $news)
                <article class="news-card">
                    <a href="{{ route('news.show', $news->slug) }}" class="news-image-link" aria-label="Leia mais sobre {{ $news->title }}">
                        <div class="news-image">
                            @if($news->thumbnail_path)
                                <img src="{{ asset('storage/' . $news->thumbnail_path) }}" alt="Miniatura da notícia: {{ $news->title }}">
                            @else
                                <img src="{{ asset('images/placeholder-thumb.jpg') }}" alt="Miniatura da notícia">
                            @endif
                        </div>
                    </a>
                    <div class="news-content">
                        <div class="news-meta">
                            @if($news->category)
                                <span class="news-meta-item news-meta-category">
                                    <a href="#">
                                        <i class="fa-solid fa-tag"></i>
                                        {{ $news->category->name }}
                                    </a>
                                </span>
                            @endif
                            <span class="news-meta-item">
                                <i class="fa-regular fa-calendar"></i>
                                {{ $news->published_at_formatted }}
                            </span>
                        </div>
                        <h3 class="news-title">
                            <a href="{{ route('news.show', $news->slug) }}">{{ $news->title }}</a>
                        </h3>
                        <p class="news-excerpt">{{ $news->excerpt }}</p>
                        <div class="news-read-more">
                            <a href="{{ route('news.show', $news->slug) }}" class="btn-read-more">
                                Saiba mais <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>
                @empty
                <div class="no-news-message">
                    <p>Nenhuma notícia recente para exibir no momento.</p>
                </div>
                @endforelse
            </div>

            <div class="news-footer">
                <a href="{{ route('news.index') }}" class="btn-all-news interactive-element">
                    Ver outras notícias
                </a>
            </div>
        </div>
    </div>
</section>