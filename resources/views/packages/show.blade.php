@extends('layouts.app')

@section('title', $package['name'])
@section('meta')
    @og('title', $package['name'])
    @og('type', 'object')
    @og('url', route('packages.show', ['namespace' => $package['packagist_namespace'], 'name' => $package['packagist_name']]))
    @og('image', url('images/package-opengraph-fallback.png'))
    @og('description', e($package['abstract']))
    @og('site_name', 'Nova Packages')
@endsection

@section('content')
    @if (session('status'))
        <div class="bg-green-lightest border border-green-light text-green-dark text-sm px-4 py-3 rounded mb-4">
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
