{{-- laravel\resources\views\partials\_post-sidebar.blade.php --}}
<aside class="post-sidebar">
                        
    {{-- Widget de Pesquisa --}}
    <div class="widget widget-search">
        <h3 class="widget-title">Pesquisar Notícias</h3>
        <form action="{{ Request::url() }}" method="GET" class="search-form">
            <div class="search-form-container">
                <input type="search" name="s" class="search-field" placeholder="Pesquisar..." value="{{ $searchTerm ?? '' }}">
                <button type="submit" class="search-button" aria-label="Pesquisar">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    @if($showRecentNewsWidget ?? true)
        <div class="widget">
            <h3 class="widget-title">Notícias Recentes</h3>
            <ul>
                @forelse($recentNews as $news)
                <li>
                    <div class="widget-post-item">
                        <a href="{{ route('news.show', $news->slug) }}" class="widget-post-thumb">
                            @if($news->thumbnail_path)
                                <img src="{{ asset('storage/' . $news->thumbnail_path) }}" alt="Miniatura da notícia: {{ $news->title }}">
                            @else
                                <img src="{{ asset('images/placeholder-thumb.jpg') }}" alt="Miniatura da notícia">
                            @endif
                        </a>
                        <h4 class="widget-post-title">
                            <div class="widget-post-meta">
                                @if($news->category)
                                    <span class="widget-post-meta-item widget-post-category">
                                        {{-- MODIFICADO: Link da categoria agora é funcional --}}
                                        <a href="{{ route('news.category', $news->category->slug) }}">
                                            <i class="fa-solid fa-tag"></i>
                                            {{ $news->category->name }}
                                        </a>
                                    </span>
                                @endif
                                <span class="widget-post-meta-item">
                                    <i class="fa-regular fa-calendar"></i>
                                    {{ $news->published_at_formatted }}
                                </span>
                            </div>
                            <a href="{{ route('news.show', $news->slug) }}">{{ $news->title }}</a>
                        </h4>
                    </div>
                </li>
                @empty
                <div class="no-news-message">
                    <p>Nenhuma notícia recente para exibir no momento.</p>
                </div>
                @endforelse
            </ul>
        </div>
    @endif

    {{-- Widget de Categorias --}}
    <div class="widget widget-list">
        <h3 class="widget-title">Editorias</h3>
        <ul>
            @forelse($categories as $category)
                {{-- MODIFICADO: Link da categoria agora é funcional --}}
                <li><a href="{{ route('news.category', $category->slug) }}">{{ $category->name }} <span>({{ $category->posts_count }})</span></a></li>
            @empty
                <li>Nenhuma categoria para exibir.</li>
            @endforelse
        </ul>
    </div>

</aside>