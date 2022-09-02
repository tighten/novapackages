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
                x-data
                type="button"
                class="self-start text-indigo-600 font-bold text-sm uppercase no-underline inline-block text-center hover:text-indigo-900 cursor-pointer"
                x-on:click="window.history.back()"
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
                        <span class="text-sm uppercase text-red-500">
                            Disabled
                        </span>
                    @endif
                </h1>
            </div>

            <x-install-box :package="$package" />
        </div>
    </div>

    <div class="container mx-auto flex flex-col items-center mb-8 px-2">
        <div class="w-full flex flex-col md:flex-row">
            <div
                @class([
                    'w-full md:w-3/4 bg-white shadow',
                    'border border-red-500' => $package['is_disabled'],
                ])
            >
                <package-detail
                    :package="{{ json_encode($package) }}"
                    :screenshots="{{ json_encode($screenshots) }}"
                />
            </div>
            <div class="w-full md:w-1/4 bg-white md:ml-4 md:mt-12 shadow text-sm border-t sm:border-t-0">
                @if ($package['current_user_owns'])
                    <a
                        href="/app/packages/{{ $package['id']}}/edit"
                        class="block bg-indigo-600 hover:bg-indigo-700 text-white no-underline font-bold p-4 md:p-8 py-4 border-gray-300 border-b"
                    >
                        Edit this package
                    </a>
                @endif

                <div class="px-4 md:px-6 py-4 border-gray-300 border-b">
                    <table class="w-full">
                        <tr>
                            <td class="font-bold py-2">Added</td>

                            <td class="py-2">
                                {{ $package['created_at'] }}
                            </td>
                        </tr>

                        @if ($package['composer_latest'])
                            <tr>
                                <td class="font-bold py-2">Last updated</td>

                                <td class="py-2">
                                    {{ now()->parse($package['composer_latest']['time'])->diffForHumans() }}
                                </td>
                            </tr>

                            <tr>
                                <td class="font-bold py-2">Version</td>

                                <td class="py-2">
                                    {{ $package['composer_latest']['version'] }}
                                </td>
                            </tr>

                            @if ($package['nova_version'])
                                <tr>
                                    <td class="py-2 font-bold">Nova Version</td>

                                    <td class="py-2">
                                        {{ $package['nova_version'] }}
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td class="font-bold pt-2" colspan="2>">Composer</td>
                            </tr>

                            <tr>
                                <td class="pb-2" colspan="2">
                                    <a
                                        href="https://packagist.org/packages/{{ $package['composer_name']}}"
                                        class="text-indigo-600 underline"
                                    >
                                        {{ $package['composer_name'] }}
                                    </a>
                                </td>
                            </tr>

                            <tr>
                                <td class="font-bold py-2">GitHub stars</td>

                                <td class="py-2">
                                    {{ $package['github_stars'] }}
                                </td>
                            </tr>

                            <tr>
                                <td class="font-bold py-2">Packagist downloads</td>

                                <td class="py-2">
                                    {{ number_format($package['packagist_downloads']) }}
                                </td>
                            </tr>
                        @endif
                    </table>

                    @if (!$package['composer_latest'])
                        <div>
                            @if ($package['composer_data'])
                                <p class="mb-2">
                                    This package is listed on
                                    <a
                                        href="https://packagist.org/packages/{{ $package['composer_name']}}.json"
                                    >
                                        the Packagist API
                                    </a>, but has no stable tags.
                                </p>
                            @else
                                <p class="mb-2">
                                    This package should be on
                                    <a
                                        href="https://packagist.org/packages/{{ $package['composer_name']}}.json"
                                    >
                                        the Packagist API
                                    </a> but we're not getting any results.
                                </p>
                            @endif

                            <p class="mb-2">
                                Please note that the Packagist cache is pretty long,
                                so some times you just need to check back in an
                                hour.
                            </p>

                            @if ($package['current_user_owns'])
                                <livewire:request-packagist-cache-refresh
                                    :package-id="$package['id']"
                                    :composer-name="$package['composer_name']"
                                />
                            @endif
                        </div>
                    @endif

                    <div>
                        @if ($package['current_user_owns'])
                            <livewire:request-repository-refresh :package-id="$package['id']" />
                        @endif
                    </div>
                </div>

                @if ($package['url'])
                    <div
                        class="p-4 md:p-6 border-solid border-gray-300 border-b overflow-hidden"
                        style="text-overflow: ellipsis;white-space: nowrap;"
                    >
                        <h3 class="uppercase text-gray-600 text-sm pb-2 font-bold">URL</h3>

                        <a
                            href="{{ $package['url'] }}"
                            class="text-indigo-600 underline"
                        >
                            {{ $package['url'] }}
                        </a>
                    </div>
                @endif

                <livewire:package-favorites
                    :favorite-count="$package['favorites_count']"
                    :is-favorite="$package['is_favorite']"
                    :package-id="$package['id']"
                />

                <livewire:rate-and-review-package
                    :average-rating="$package['average_rating']"
                    :rating-count="$package['rating_count']"
                    :rating-counts="$package['rating_counts']"
                    :review-url="route('reviews.create', ['namespace' => $package['packagist_namespace'], 'name' => $package['packagist_name']])"
                />

                <div class="p-4 md:p-6 border-gray-300 border-b">
                    <h3 class="uppercase text-gray-600 text-sm font-bold">Author</h3>

                    <div class="flex text-sm pt-4 items-center">
                        <img
                            src="{{ $package['author']['avatar_url'] }}"
                            class="rounded-full h-10 w-10 mr-4"
                            alt="{{ $package['author']['name'] }}"
                        />

                        <a
                            href="/collaborators/{{ $package['author']['github_username'] }}"
                            class="text-indigo-600 font-bold no-underline uppercase text-sm hover:text-indigo-700"
                        >
                            {{ $package['author']['name'] }}
                        </a>
                    </div>
                </div>

                @if ($package['contributors'])
                    <div
                        class="p-4 pb-2 md:p-6 border-gray-300 border-b"
                    >
                        <h3 class="uppercase text-gray-600 text-sm font-bold">
                            Contributors
                        </h3>

                        @foreach($package['contributors'] as $contributor)
                            <div class="flex text-sm pt-4 items-center">
                                <x-title-icon :title="$contributor['name']" size="medium" :image="$contributor['avatar_url']" />

                                <span class="text-indigo-600 font-bold no-underline uppercase text-sm hover:text-indigo-700">
                                    {{ $contributor['name'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="p-4 pb-0 md:p-6 md:pb-2 border-gray-300 border-b">
                    <h3 class="uppercase text-gray-600 text-sm font-bold">Tags</h3>

                    <div class="block py-4">
                        @foreach($package['tags'] as $tag)
                            <a
                                href="{{ $tag['url'] }}"
                                class="bg-indigo-200 text-indigo-600 rounded-l-full rounded-r-full px-4 py-2 mr-2 mb-2 inline-block font-bold"
                            >
                                {{ $tag['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
