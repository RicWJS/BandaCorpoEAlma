{{-- laravel\resources\views\news.blade.php --}}
@extends('layouts.app')

@section('title', 'Notícias - Banda Corpo e Alma')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/news.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site/post-detail.css') }}">
@endpush

@section('content')

    @include('includes.header')

    <main>       
        <section class="news-listing-section">
            <div class="section-container">
                <div class="section-title">
                    <h1>Notícias</h1>
                </div>

                <div class="news-listing-layout">
                    
                    <div class="news-listing-main">

                        @if(!empty($searchTerm))
                            <div class="search-results-header">
                                @if($posts->total() > 0)
                                    <h3 class="search-results-title">
                                        {{ $posts->total() }} {{ $posts->total() == 1 ? 'resultado encontrado' : 'resultados encontrados' }} para: <strong>"{{ $searchTerm }}"</strong>
                                    </h3>
                                @else
                                    <h3 class="search-results-title">
                                        Nenhum resultado encontrado para: <strong>"{{ $searchTerm }}"</strong>
                                    </h3>
                                @endif
                            </div>
                        @endif

                        <div class="news-card-list">
                            
                            @forelse ($posts as $post)
                                <article class="news-card-item">
                                    <a href="{{ route('news.show', $post->slug) }}" class="news-card-thumb-link">
                                        <div class="news-card-thumb">
                                            @if($post->thumbnail_path)
                                                <img src="{{ asset('storage/' . $post->thumbnail_path) }}" alt="{{ $post->title }}">
                                            @else
                                                <img src="{{ asset('images/placeholder-thumb.jpg') }}" alt="Imagem padrão">
                                            @endif
                                        </div>
                                    </a>
                                    <div class="news-card-content">
                                        <div class="news-card-meta">
                                            @if($post->category)
                                                <span class="news-card-meta-item news-card-category">
                                                    {{-- MODIFICADO: Link da categoria agora é funcional --}}
                                                    <a href="{{ route('news.category', $post->category->slug) }}">
                                                        <i class="fa-solid fa-tag"></i>
                                                        {{ $post->category->name }}
                                                    </a>
                                                </span>
                                            @endif
                                            <span class="news-card-meta-item">
                                                <i class="fa-regular fa-calendar"></i>
                                                {{ $post->published_at_formatted }}
                                            </span>
                                        </div>
                                        <h2 class="news-card-title">
                                            <a href="{{ route('news.show', $post->slug) }}">{{ $post->title }}</a>
                                        </h2>
                                        <div class="news-card-excerpt">
                                            <p>{{ $post->excerpt }}</p>
                                        </div>
                                        <div class="news-read-more">
                                            <a href="{{ route('news.show', $post->slug) }}" class="btn-read-more">
                                                Saiba mais <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                @if(empty($searchTerm))
                                    <div class="no-news-message">
                                        <p>Nenhuma notícia para exibir no momento.</p>
                                    </div>
                                @endif
                            @endforelse

                        </div>

                        <div class="pagination-container">
                            {{ $posts->links('pagination.custom') }}
                        </div>

                    </div>

                    {{-- Sidebar --}}
                    @include('partials._post-sidebar', [
                        'showRecentNewsWidget' => false,
                        'searchTerm' => $searchTerm ?? null
                    ])
                </div>
            </div>
        </section>
    </main>
    
    @include('includes.footer')

@endsection