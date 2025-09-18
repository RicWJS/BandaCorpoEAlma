{{-- laravel\resources\views\home.blade.php --}}
@extends('layouts.app')

@section('title', 'Home - Banda Corpo e Alma')

@section('content')

@include('includes.header')
@include('includes.bannerSection')
@include('includes.recentNewsSection')
@include('includes.spotifySection')
@include('includes.footer')

@endsection
