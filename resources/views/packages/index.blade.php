@extends('layouts.app')

@section('title', 'Discover new packages for Laravel Nova')
@section('meta')
    @og('title', 'Discover new packages for Laravel Nova')
    @og('type', 'website')
    @og('url', url('/'))
    @og('image', url('images/nova-packages-opengraph.png'))
    @og('description', 'Discover new packages for Laravel Nova. Search, browse, or submit your own packages.')
    @og('site_name', 'Nova Packages')

    <meta name="description" content="Discover new packages for Laravel Nova. Search, browse, or submit your own packages" />
    <meta name="twitter:card" content="summary_large_image">
@endsection

@section('content')
    <div class="container mx-auto mb-4">
        <img src="/images/hero.svg" alt="Laravel Nova hero" class="w-full md:w-2/3 lg:w-1/2 mx-auto block">
    </div>
    @if (session('status'))
    <div class="bg-green-100 border border-green-300 text-green-600 text-sm px-4 py-3 rounded mb-4">
        {{ session('status') }}
    </div>
    @endif

    @livewire('package-list')
@endsection
