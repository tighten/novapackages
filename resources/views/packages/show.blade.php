@extends('layouts.app')

@section('top-id', 'vue-app')

@section('title', $package['name'])
@section('meta')
    @og('title', $package['name'])
    @og('type', 'object')
    @og('url', route('packages.show', ['namespace' => $package['packagist_namespace'], 'name' => $package['packagist_name']]))
    @og('image', url(config('opengraph.image_directory_name') . "/{$packageOgImage}"))
    @og('description', e($package['abstract']))
    @og('site_name', 'Nova Packages')

    <meta name="twitter:card" content="summary_large_image">
@endsection

@section('content')
    @if (session('status'))
        <div class="px-4 py-3 mb-4 text-sm text-green-600 bg-green-100 border border-green-300 rounded">
            {{ session('status') }}
        </div>
    @endif

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
