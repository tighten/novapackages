<div
    id="top"
    class="container mx-auto flex flex-col items-center mb-8 px-2"
>
    <div class="w-full">
        @if (url()->previous() !== url()->current())
            <a
                href="{{ url()->previous() }}"
                class="self-start text-indigo-600 font-bold text-sm uppercase no-underline inline-block text-center hover:text-indigo-900 cursor-pointer"
            >&#8592; Back</a>
        @else
            <a
                href="{{ route('home') }}"
                class="self-start text-indigo-600 font-bold text-sm uppercase no-underline inline-block text-center hover:text-indigo-900 cursor-pointer"
            >&#8592; Home</a>
        @endif
    </div>
    <div class="w-full flex flex-col md:flex-row items-center justify-between pt-2 md:pt-6 md:pb-8">
        <div class="flex flex-row items-center mb-4 sm:mb-0">
            @include('livewire.partials.title-icon', [
                'size' => 'large',
                'title' => $package['name'],
            ])
            <h1 class="inline text-gray-800 text-2xl md:text-4xl font-bold">
                {{ $package['name'] }}
                @if ($package['is_disabled'])
                    <span class="text-xs uppercase text-red">Disabled</span>
                @endif
            </h1>
        </div>
        <div
            class="relative"
            x-data="{ open: @entangle('showInstallDropdown') }"
            x-on:click.away="open = false"
        >
            <a
                class="block cursor-pointer md:inline-block w-full md:w-auto py-4 px-4 sm:px-6 bg-indigo-600 text-white md:rounded-l-full md:rounded-r-full no-underline hover:bg-indigo-700 flex flex-row justify-center items-center content-center"
                x-on:click="open = ! open"
            >
                <svg
                    class="mr-4 inline-block fill-current w-4"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                >
                    <path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/>
                </svg>
                <span class="mr-4 inline-block leading-none inline-block uppercase">Install</span>
                <svg
                    x-show="open"
                    x-cloak
                    class="inline-block fill-current w-4 h-4"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                >
                    <path d="M10.707 7.05L10 6.343 4.343 12l1.414 1.414L10 9.172l4.243 4.242L15.657 12z"/>
                </svg>
                <svg
                    x-show="! open"
                    class="inline-block fill-current w-4"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                >
                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                </svg>
            </a>
            <div
                x-show="open"
                x-cloak
                class="absolute shadow rounded bg-white right-0"
                style="top: calc(100% + 1rem); min-width: 380px;"
            >
                <div
                    class="flex flex-row w-full p-6 px-8 items-center text-gray-500"
                    x-data="{ copySuccessful: false }"
                >
                    <input
                        id="packagist-install"
                        type="text"
                        class="rounded flex-grow block border py-2 px-2 mr-4 font-mono text-xs text-black outline-none"
                        x-bind:class="{ 'border-green-500 border-2': copySuccessful }"
                        value="composer require {{ $package['composer_name'] }}"
                    />
                    <svg
                        x-show="copySuccessful"
                        class="fill-current w-6 h-6 hover:text-indigo-600 pointer-cursor"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                    >
                        <path d="M0 11l2-2 5 5L18 3l2 2L7 18z"/>
                    </svg>
                    <svg
                        x-show="! copySuccessful"
                        id="copy-button"
                        class="fill-current w-6 h-6 hover:text-indigo-600 pointer-cursor"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        data-clipboard-target="#packagist-install"
                        x-on:click="copySuccessful = true; setTimeout(() => {copySuccessful = false;}, 3000)"
                    >
                        <path d="M6 6V2c0-1.1.9-2 2-2h10a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-4v4a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V8c0-1.1.9-2 2-2h4zm2 0h4a2 2 0 0 1 2 2v4h4V2H8v4zM2 8v10h10V8H2z"/>
                    </svg>
                </div>
                @if ($package['composer_data']['package'])
                    <div
                        class="border border-t border-gray-300er flex flex-row flex-no-wrap"
                    >
                        <a
                            href="{{ $package['composer_data']['package']['repository'] }}"
                            class="no-underline text-center text-indigo-600 uppercase text-sm font-bold w-1/2 py-4 border-r border-gray-300 hover:bg-indigo-100"
                        >
                            GitHub
                        </a>
                        @if ($package['composer_latest'] && $package['composer_latest']['dist'])
                            <a
                                href="{{ $package['composer_latest']['dist']['url'] }}"
                                class="no-underline text-center text-indigo-600 uppercase text-sm font-bold w-1/2 py-4 hover:bg-indigo-100"
                            >
                                Download Zip
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="w-full flex flex-col md:flex-row">
        <div
            @class([
                'w-full md:w-3/4 bg-white shadow border',
                'border-red-500' => $package['is_disabled'],
            ])
        >
            <livewire:package-details :package="$package" :screenshots="$screenshots" />
        </div>
        <div class="w-full md:w-1/4 bg-white md:ml-4 md:mt-12 shadow text-sm border-t sm:border-t-0">
            @if ($package['current_user_owns'])
                <a
                    href="{{ route('app.packages.edit', $package['id']) }}"
                    class="block bg-indigo-600 hover:bg-indigo-700 text-white no-underline font-bold p-4 md:p-8 py-4 border-gray-300 border-b"
                >
                    Edit this package
                </a>
            @endif
            <div class="px-4 md:px-6 py-4 border-gray-300 border-b">
                <table class="w-full">
                    <tr>
                        <td class="font-bold py-2">Added</td>
                        <td class="py-2">{{ $package['created_at'] }}</td>
                    </tr>
                    @if ($package['composer_latest'])
                        <tr>
                            <td class="font-bold py-2">Last updated</td>
                            <td class="py-2">
                                {{ \Illuminate\Support\Facades\Date::parse($package['composer_latest']['time'])->diffForHumans() }}
                            </td>
                        </tr>
                        <tr>
                            <td class="font-bold py-2">Version</td>
                            <td class="py-2">
                                {{ $package['composer_latest']['version'] }}
                            </td>
                        </tr>
                        <tr>
                            <td class="font-bold pt-2" colspan="2>">Composer</td>
                        </tr>
                        <tr>
                            <td class="pb-2" colspan="2">
                                <a
                                    href="https://packagist.org/packages/{{ $package['composer_name'] }}"
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
                            <td class="py-2">{{ number_format($package['packagist_downloads']) }}</td>
                        </tr>
                    @endif
                </table>
                @if (! $package['composer_latest'])
                    <div x-data="{ packagistRefreshRequested: @entangle('packagistRefreshRequested') }">
                        @if ($package['composer_data'])
                            <p class="mb-2">
                                This package is listed on <a href="https://packagist.org/packages/{{ $package['composer_name'] }}.json">the Packagist API</a>, but has no stable tags.
                            </p>
                        @else
                            <p class="mb-2">
                                This package should be on <a href="https://packagist.org/packages/{{ $package['composer_name'] }}.json">the Packagist API</a>, but we're not getting any results.
                            </p>
                        @endif
                        <p class="mb-2">
                            Please note that the Packagist cache is pretty long, so some times you just need to check back in an hour.
                        </p>
                        @if ($package['current_user_owns'])
                            <button
                                x-show="! packagistRefreshRequested"
                                wire:click="requestPackagistRefresh"
                                class="block mt-8 mb-2 text-left"
                            >
                                Request a cache refresh from Packagist (the cache lasts 5 minutes)
                            </button>
                        @endif
                        <span x-show="packagistRefreshRequested" x-cloak class="block mt-8 mb-2">
                            Refresh requested
                        </span>
                    </div>
                @endif
                <div x-data="{ repositoryRefreshRequested: @entangle('repositoryRefreshRequested') }">
                    @if ($package['current_user_owns'])
                        <button
                            x-show="! repositoryRefreshRequested"
                            wire:click="requestRepositoryRefresh"
                            class="block mt-8 mb-2 text-left"
                        >
                            Request a refresh of the readme from your package registry or VCS provider.
                        </button>
                    @endif
                    <span
                        x-show="repositoryRefreshRequested"
                        x-cloak
                        class="block mt-8 mb-2"
                    >
                        Refresh requested
                    </span>
                </div>
            </div>
            @if ($package['url'])
                <div
                    class="p-4 md:p-6 border-solid border-gray-300 border-b overflow-hidden"
                    style="text-overflow: ellipsis;white-space: nowrap;"
                >
                    <h3 class="uppercase text-gray-600 text-sm pb-2 font-bold">URL</h3>
                    <a href="{{ $package['url'] }}" class="text-indigo-600 underline">
                        {{ $package['url'] }}
                    </a>
                </div>
            @endif
            <div class="p-4 pb-0 md:p-6 md:pb-2 border-gray-300 border-b">
                <h3 class="uppercase text-gray-600 text-sm font-bold">Favorites</h3>
                <div class="block py-4">
                    {{ $package['favorites_count'] }} {{ Str::plural('user', $package['favorites_count']) }} favorited
                </div>
                @auth
                    <button
                        wire:click="favorite"
                        class="block text-indigo-600 no-underline font-bold text-sm cursor-pointer pb-4"
                    >
                        {{ $package['is_favorite'] ? 'Remove Favorite' : 'Add to Favorites' }}
                    </button>
                @endauth
            </div>
            <div class="p-4 md:p-6 pb-4 border-gray-300 border-b">
                <h3 class="uppercase text-gray-600 text-sm font-bold">Rating</h3>
                <div class="flex">
                    <div class="mt-2 mb-4 text-5xl w-1/2">
                        @if ($package['average_rating'])
                            {{ $package['average_rating'] }}
                        @else
                            None yet
                        @endif
                    </div>
                    <div class="w-1/2 mb-6 text-gray-500 self-end">(out of 5)</div>
                </div>
                @auth
                    @if (! $package['is_self_authored'] && ! $package['is_self_contributed'])
                        <div class="mb-4 flex items-center">
                            <div class="w-1/3 text-gray-600">Tap to rate:</div>
                            <livewire:star-rating :package="$package" :rating="$package['current_user_rating']" :read-only="false" />
                        </div>
                    @endif
                @endauth
                @foreach ($package['rating_counts'] as $ratingCount)
                    <div class="flex my-1">
                        <div class="w-1/3 text-xs pr-1 text-gray-500 text-right">
                            @foreach (range(1, $ratingCount['number']) as $number)
                                <svg
                                    class="inline-block text-gray-400 fill-current h-2 w-2 cursor-pointer"
                                    xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                    viewBox="0 0 20 20"
                                >
                                    <g stroke="none" stroke-width="1" fill-rule="evenodd">
                                        <g>
                                            <polygon id="Star-3" points="10 15 4.12214748 18.0901699 5.24471742 11.545085 0.489434837 6.90983006 7.06107374 5.95491503 10 0 12.9389263 5.95491503 19.5105652 6.90983006 14.7552826 11.545085 15.8778525 18.0901699"></polygon>
                                        </g>
                                    </g>
                                </svg>

                            @endforeach
                        </div>
                        <div class="w-2/3 w-full bg-gray-200 h-2 mt-1">
                            <div class="bg-yellow-500 h-2" style="width: {{ $package['total_number_of_ratings'] === 0 ? 0 : $ratingCount['count'] / $package['rating_count'] * 100 }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="text-right text-sm text-gray-600 mt-2 mb-2">
                    {{ $package['rating_count'] }} ratings
                </div>
                @if (auth()->check() && ! $package['current_user_review'] && ! $package['is_self_authored'] && ! $package['is_self_contributed'])
                    <div>
                        <a
                            class="block text-indigo-600 no-underline font-bold text-sm cursor-pointer pb-4"
                            href="{{ route('reviews.create', [$package['packagist_namespace'], $package['packagist_name']]) }}"
                        >
                            Review This Package
                        </a>
                    </div>
                @endif
            </div>
            <div class="p-4 md:p-6 border-gray-300 border-b">
                <h3 class="uppercase text-gray-600 text-sm font-bold">Author</h3>
                <div class="flex text-sm pt-4 items-center">
                    <img
                        src="{{ $package['author']['avatar_url'] }}"
                        class="rounded-full h-10 w-10 mr-4"
                        alt="{{ $package['author']['name'] }}"
                    />
                    <a
                        href="{{ route('collaborators.show', $package['author']['github_username']) }}"
                        class="text-indigo-600 font-bold no-underline uppercase text-sm hover:text-indigo-700"
                    >
                        {{ $package['author']['name'] }}
                    </a>

                </div>
            </div>
            @if ($package['contributors'])
                <div class="p-4 pb-2 md:p-6 border-gray-300 border-b">
                    <h3 class="uppercase text-gray-600 text-sm font-bold">Contributors</h3>
                    @foreach ($package['contributors'] as $contributor)
                        <div class="flex text-sm pt-4 items-center">
                            <img
                                src="{{ $contributor['avatar_url'] }}"
                                class="rounded-full h-10 w-10 mr-4"
                                alt="{{ $contributor['name'] }}"
                            />
                            <a class="text-indigo-600 font-bold no-underline uppercase text-sm hover:text-indigo-700">
                                {{ $contributor['name'] }}
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="p-4 pb-0 md:p-6 md:pb-2 border-gray-300 border-b">
                <h3 class="uppercase text-gray-600 text-sm font-bold">Tags</h3>
                <div class="block py-4">
                    @foreach ($package['tags'] as $tag)
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
