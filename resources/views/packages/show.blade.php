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

    <div
        id="top"
        class="container mx-auto flex flex-col items-center px-2"
    >
        <div class="w-full">
            <button
                type="button"
                class="self-start text-indigo-600 font-bold text-sm uppercase no-underline inline-block text-center hover:text-indigo-900 cursor-pointer"
                {{--@click="goBack"--}}
            >
                &#8592; Back
            </button>
        </div>

        <div class="w-full flex flex-col md:flex-row items-center justify-between pt-2 md:pt-6 md:pb-8">
            <div class="flex flex-row items-center mb-4 sm:mb-0">
                <x-title-icon :title="$package['name']" size="large" />

                <h1 class="inline text-gray-800 text-2xl md:text-4xl font-bold">
                    {{ $package['name'] }}
                    @if ($package['is_disabled'])
                        <span class="text-xs uppercase text-red">
                            Disabled
                        </span>
                    @endif
                </h1>
            </div>

            <x-install-box />
        </div>
    </div>

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
