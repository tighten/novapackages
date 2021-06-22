@extends('layouts.app')

@section('top-id', 'vue-app')

@section('title', $package['name'])
@section('meta')
    @og('title', $package['name'])
    @og('type', 'object')
    @og('url', route('packages.show', ['namespace' => $package['packagist_namespace'], 'name' => $package['packagist_name']]))
    @og('image', $packageOgImageUrl)
    @og('description', e($package['abstract']))
    @og('site_name', 'Nova Packages')

    <meta name="twitter:card" content="summary_large_image">
@endsection

@section('content')
    <x-status class="container mx-auto mb-4"/>

    <package-detail-frame
        :auth="{{ auth()->check() ? 'true' : 'false' }}"
        :auth-id="{{ auth()->check() ? auth()->id() : 'null' }}"
        :initial-package="{{ json_encode($package) }}"
    >
        <package-detail
            :package="{{ json_encode($package) }}"
            :screenshots="{{ json_encode($screenshots) }}"
        />
    </package-detail-frame>
@endsection
