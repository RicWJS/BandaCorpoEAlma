{{-- resources/views/news-detail.blade.php --}}
@extends('layouts.app')

@section('title', $post->title . ' - Banda Corpo e Alma')

{{-- =========== CÓDIGO ADICIONADO =========== --}}
@push('meta_tags')
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $post->title }}">
    <meta property="og:description" content="{{ $post->excerpt }}">
    @if($post->image_path)
        <meta property="og:image" content="{{ asset('storage/' . $post->image_path) }}">
    @endif

    <meta name="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $post->title }}">
    <meta property="twitter:description" content="{{ $post->excerpt }}">
    @if($post->image_path)
        <meta name="twitter:image" content="{{ asset('storage/' . $post->image_path) }}">
    @endif
@endpush
{{-- ========================================= --}}

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/post-detail.css?v=2') }}">
@endpush

@section('content')

    @include('includes.header')

    <main>      
        <section class="post-detail-section">
            <div class="section-container">
                <div class="section-title">
                    <h2>Notícias</h2>
                </div>

                <div class="post-layout-container">
                    
                    <div class="post-main-content">
                        <article>
                            <div class="post-header">
                                <div class="post-meta-data">
                                    @if($post->category)
                                        <span class="post-category post-meta-item">
                                            {{-- MODIFICADO: Link da categoria agora é funcional --}}
                                            <a href="{{ route('news.category', $post->category->slug) }}">
                                                <i class="fa-solid fa-tag"></i>
                                                {{ $post->category->name }}
                                            </a>
                                        </span>
                                    @endif
                                    <span class="post-date post-meta-item">
                                        <i class="fa-regular fa-calendar"></i>
                                        {{ $post->published_at_formatted }}
                                    </span>
                                </div>
                                
                                <h1 class="post-detail-title">{{ $post->title }}</h1>

                                <h2 class="post-detail-excerpt">{{ $post->excerpt }}</h2>
                            </div>

                            @if($post->image_path)
                                <div class="post-image-container">
                                    <img src="{{ asset('storage/' . $post->image_path) }}" alt="{{ $post->title }}">
                                </div>
                            @endif

                            <div class="post-body">
                                {!! $post->content !!}
                            </div>
                            
                            <div class="post-share-section">
                                <h3>Compartilhe:</h3>
                                <div class="social-share-buttons">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="btn-facebook"><i class="fab fa-facebook-f"></i> Facebook</a>
                                    <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' ' . url()->current()) }}" target="_blank" class="btn-whatsapp"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                                    
                                    {{-- =========== CÓDIGO ADICIONADO =========== --}}
                                    <button type="button" id="copyLinkBtn" class="btn-copy-link" title="Copiar link da notícia">
                                        <i class="fa-solid fa-link"></i> Copiar Link
                                    </button>
                                    {{-- ========================================= --}}

                                </div>
                            </div>
                        </article>
                    </div>

                    {{-- Sidebar --}}
                    @include('partials._post-sidebar')
                </div>
            </div>
        </section>
    </main>
    
    @include('includes.footer')


    {{-- =========== SCRIPT ADICIONADO =========== --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const copyButton = document.getElementById('copyLinkBtn');

            if (copyButton) {
                copyButton.addEventListener('click', () => {
                    // Usa a URL canônica gerada pelo Laravel para garantir consistência
                    const linkToCopy = "{{ url()->current() }}";
                    const originalHtml = copyButton.innerHTML;

                    navigator.clipboard.writeText(linkToCopy)
                        .then(() => {
                            // Feedback visual de sucesso
                            copyButton.innerHTML = '<i class="fa-solid fa-check"></i> Link Copiado!';
                            copyButton.disabled = true;

                            // Retorna ao estado original após 2.5 segundos
                            setTimeout(() => {
                                copyButton.innerHTML = originalHtml;
                                copyButton.disabled = false;
                            }, 2500);
                        })
                        .catch(err => {
                            console.error('Falha ao copiar o link para a área de transferência: ', err);
                            // Feedback visual de erro (opcional)
                            copyButton.innerHTML = 'Erro ao Copiar';
                            setTimeout(() => {
                                copyButton.innerHTML = originalHtml;
                            }, 2500);
                        });
                });
            }
        });
    </script>
    {{-- ========================================= --}}

@endsection