{{-- laravel\resources\views\contact.blade.php --}}
@extends('layouts.app')

@section('title', 'Contato - Banda Corpo e Alma')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site/contact.css') }}">
@endpush

@section('content')

    @include('includes.header')

    <main>       
        <section class="contact-section">
            <div class="section-container">
                <div class="section-title">
                    <h1>Fale com a gente</h1>
                </div>

                
            </div>
        </section>
    </main>
    
    @include('includes.footer')

@endsection