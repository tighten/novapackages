@extends('layouts.app')

@section('title', $package->display_name)
@section('meta')
    @og('title', $package->display_name)
    @og('type', 'object')
    @og('url', route('packages.show', ['namespace' => $package->composer_vendor, 'name' => $package->composer_package]))
    @og('image', $packageOgImageUrl)
    @og('description', e($package->abstract))
    @og('site_name', 'Nova Packages')

    <meta name="twitter:card" content="summary_large_image">
@endsection

@section('content')
    <x-status class="container mx-auto mb-4"/>

    @livewire('package-show', ['package' => $package])
@endsection
