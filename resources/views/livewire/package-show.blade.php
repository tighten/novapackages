<div
    id="top"
    class="container mx-auto flex flex-col items-center mb-8 px-2"
    x-data="{
        installBoxOpen: false,
        copyWasSuccessful: false,
        composerString: 'composer require {{ $package->composer_name }}',
        copyTextToClipboard() {
            const onSuccess = () => {
                this.copyWasSuccessful = true;
                setTimeout(() => this.copyWasSuccessful = false, 3000);
            };

            if (navigator.clipboard) {
                navigator.clipboard.writeText(this.composerString).then(onSuccess).catch(() => {
                    this.fallbackCopy();
                });
            } else {
                this.fallbackCopy();
            }
        },
        fallbackCopy() {
            const input = document.getElementById('packagist-install');
            input.select();
            input.setSelectionRange(0, 99999);
            document.execCommand('copy');
            this.copyWasSuccessful = true;
            setTimeout(() => this.copyWasSuccessful = false, 3000);
        }
    }"
>
    <div class="w-full">
        <button
            type="button"
            class="self-start text-indigo-600 font-bold text-sm uppercase no-underline inline-block text-center hover:text-indigo-900 cursor-pointer"
            onclick="window.history.back()"
        >
            &#8592; Back
        </button>
    </div>

    <div class="w-full flex flex-col md:flex-row items-center justify-between pt-2 md:pt-6 md:pb-8">
        <div class="flex flex-row items-center mb-4 sm:mb-0">
            @include('livewire.partials.title-icon', [
                'title' => $package->name,
                'size' => 'large',
            ])

            <h1 class="inline text-gray-800 text-2xl md:text-4xl font-bold">
                {{ $package->display_name }}
                @if ($package->is_disabled)
                    <span class="text-xs uppercase text-red-600">Disabled</span>
                @endif
            </h1>
        </div>

        <div class="relative" @click.away="installBoxOpen = false">
            <a
                class="block cursor-pointer md:inline-block w-full md:w-auto py-4 px-4 sm:px-6 bg-indigo-600 text-white md:rounded-l-full md:rounded-r-full no-underline hover:bg-indigo-700 flex flex-row justify-center items-center content-center"
                @click="installBoxOpen = !installBoxOpen"
            >
                <svg class="mr-4 inline-block fill-current w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z" />
                </svg>

                <span class="mr-4 inline-block leading-none inline-block uppercase">Install</span>

                <svg x-show="installBoxOpen" class="inline-block fill-current w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M10.707 7.05L10 6.343 4.343 12l1.414 1.414L10 9.172l4.243 4.242L15.657 12z" />
                </svg>

                <svg x-show="!installBoxOpen" class="inline-block fill-current w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                </svg>
            </a>

            <div
                class="absolute shadow-sm rounded-sm bg-white right-0"
                :class="installBoxOpen ? 'visible' : 'hidden'"
                style="top: calc(100% + 1rem); min-width: 380px;"
            >
                <div class="flex flex-row w-full p-6 px-8 items-center text-gray-500">
                    <input
                        id="packagist-install"
                        type="text"
                        class="rounded-sm grow block border border-gray-200 py-2 px-2 mr-4 font-mono text-xs text-black outline-hidden"
                        :class="copyWasSuccessful ? 'border-green-600 border-2' : ''"
                        :value="composerString"
                        readonly
                    />

                    <svg
                        x-show="copyWasSuccessful"
                        class="fill-current w-6 h-6 text-green-600"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                    >
                        <path d="M0 11l2-2 5 5L18 3l2 2L7 18z" />
                    </svg>

                    <svg
                        x-show="!copyWasSuccessful"
                        class="fill-current w-6 h-6 hover:text-indigo-600 cursor-pointer"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        @click="copyTextToClipboard()"
                    >
                        <path d="M6 6V2c0-1.1.9-2 2-2h10a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-4v4a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V8c0-1.1.9-2 2-2h4zm2 0h4a2 2 0 0 1 2 2v4h4V2H8v4zM2 8v10h10V8H2z" />
                    </svg>
                </div>

                @if ($packagistData && isset($packagistData['package']))
                    <div class="border border-t border-gray-300 flex flex-row flex-no-wrap">
                        <a
                            href="{{ $packagistData['package']['repository'] ?? '#' }}"
                            class="no-underline text-center text-indigo-600 uppercase text-sm font-bold w-1/2 py-4 border-r border-gray-300 hover:bg-indigo-100"
                        >GitHub</a>

                        @if ($composerLatest && isset($composerLatest['dist']['url']))
                            <a
                                href="{{ $composerLatest['dist']['url'] }}"
                                class="no-underline text-center text-indigo-600 uppercase text-sm font-bold w-1/2 py-4 hover:bg-indigo-100"
                            >Download Zip</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="w-full flex flex-col md:flex-row">
        {{-- Main Content Area --}}
        <div
            class="w-full md:w-3/4 bg-white shadow-sm"
            :class="'{{ $package->is_disabled ? 'border-red-600' : 'border-indigo-600' }}'"
        >
            @if (! $creatingReview)
                {{-- Package Detail Content --}}
                @if ($possiblyAbandoned)
                    <div class="p-4 text-white bg-red-600">
                        This package is possibly abandoned. Please proceed with care.
                    </div>
                @endif

                @if ($package->marked_as_unavailable_at)
                    <div class="p-4 text-white bg-orange-500">
                        This package seems to have a broken documentation URL. Please proceed with care.
                    </div>
                @endif

                <div class="bg-gray-700">
                    <ul class="flex">
                        <li class="ml-4">
                            <a class="block p-4 font-semibold text-gray-100 no-underline bg-gray-800 hover:bg-gray-100 hover:text-gray-800 sm:mr-2 md:mr-4 md:px-6" href="#readme">Readme</a>
                        </li>

                        @if ($package->screenshots->count())
                            <li>
                                <a class="block p-4 font-semibold text-gray-100 no-underline bg-gray-800 hover:bg-gray-100 hover:text-gray-800 sm:mr-2 md:mr-4 md:px-6" href="#screenshots">Screenshots</a>
                            </li>
                        @endif

                        @if ($package->reviews->count())
                            <li>
                                <a class="block p-4 font-semibold text-gray-100 no-underline bg-gray-800 hover:bg-gray-100 hover:text-gray-800 sm:mr-2 md:mr-4 md:px-6" href="#reviews">Reviews</a>
                            </li>
                        @endif
                    </ul>
                </div>

                @if (auth()->user()?->isAdmin())
                    <div class="-mb-8 text-right">
                        <x-admin-dropdown>
                            <x-slot:trigger>
                                <span class="flex items-center inline-block font-medium text-gray-600 appearance-none">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                    </svg>
                                </span>
                            </x-slot:trigger>

                            <div class="overflow-hidden bg-indigo-600 border border-gray-200 rounded-sm shadow-sm">
                                @if ($package->is_disabled)
                                    <a
                                        href="{{ route('app.admin.enable-package', $package) }}"
                                        class="block px-4 py-3 text-white no-underline bg-indigo-600 border-b border-gray-200 hover:text-white hover:bg-blue-500"
                                    >Enable</a>
                                @else
                                    <a
                                        href="{{ route('app.admin.disable-package', $package) }}"
                                        class="block px-4 py-3 text-white no-underline bg-indigo-600 border-b border-gray-200 hover:text-white hover:bg-blue-500"
                                    >Disable</a>
                                @endif
                            </div>
                        </x-admin-dropdown>
                    </div>
                @endif

                <div class="m-4 wrap-break-word md:m-10">
                    @if ($instructions)
                        <div class="pb-6 border-b border-gray-300">
                            <h2 class="py-2 pt-3 pl-4 mb-4 -mx-4 text-2xl font-bold text-gray-800 bg-gray-200 border-b-2 border-gray-300">
                                Installation Instructions
                            </h2>

                            <div class="min-h-full mb-4 bg-white markdown-body">
                                {!! $instructions !!}
                            </div>
                        </div>
                    @endif

                    <div class="pb-6 border-b border-gray-300">
                        <h2
                            id="readme"
                            class="py-2 pt-3 pl-4 mt-8 mb-4 -mx-4 text-xl font-bold text-gray-800 bg-gray-200 border-b-2 border-gray-300 md:text-2xl"
                        >
                            Readme
                        </h2>

                        <div class="min-h-full bg-white markdown-body">
                            {!! $readme !!}
                        </div>
                    </div>

                    @if ($package->screenshots->count())
                        <div class="pb-6 border-b border-gray-300">
                            <h2
                                id="screenshots"
                                class="py-2 pt-3 pl-4 mt-8 mb-4 -mx-4 text-2xl font-bold text-gray-800 bg-gray-200 border-b-2 border-gray-300"
                            >
                                Screenshots
                            </h2>

                            <x-screenshot-gallery :screenshots="$package->screenshots" />
                        </div>
                    @endif

                    @if ($package->reviews->count())
                        <div class="pb-6 border-b border-gray-300">
                            <h2
                                id="reviews"
                                class="py-2 pt-3 pl-4 mt-8 mb-4 -mx-4 text-xl font-bold text-gray-800 bg-gray-200 border-b-2 border-gray-300 md:text-2xl"
                            >
                                Reviews
                            </h2>

                            @livewire('review-list', ['packageId' => $package->id], key('review-list-' . $package->id))
                        </div>
                    @endif

                    <a
                        href="#top"
                        class="block mt-8 font-semibold text-center text-indigo-600 no-underline hover:text-gray-800"
                    >
                        Back to Top
                    </a>
                </div>
            @else
                {{-- Review Create Form --}}
                @livewire('package-review-create', [
                    'packageId' => $package->id,
                    'initialRating' => $currentUserRating ?: null,
                ], key('review-create-' . $package->id))
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="w-full md:w-1/4 bg-white md:ml-4 md:mt-12 shadow-sm text-sm border-t sm:border-t-0">
            @if ($currentUserOwns)
                <a
                    href="{{ route('app.packages.edit', $package) }}"
                    class="block bg-indigo-600 hover:bg-indigo-700 text-white no-underline font-bold p-4 md:p-8 py-4 border-gray-300 border-b"
                >
                    Edit this package
                </a>
            @endif

            <div class="px-4 md:px-6 py-4 border-gray-300 border-b">
                <table class="w-full">
                    <tr>
                        <td class="font-bold py-2">Added</td>
                        <td class="py-2">{{ $package->created_at->diffForHumans() }}</td>
                    </tr>

                    @if ($composerLatest)
                        <tr>
                            <td class="font-bold py-2">Last updated</td>
                            <td class="py-2">
                                @if (isset($composerLatest['time']))
                                    {{ \Illuminate\Support\Str::title(\Carbon\Carbon::parse($composerLatest['time'])->diffForHumans()) }}
                                @else
                                    Nothing yet.
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td class="font-bold py-2">Version</td>
                            <td class="py-2">{{ $composerLatest['version'] }}</td>
                        </tr>

                        @if ($novaVersion)
                            <tr>
                                <td class="py-2 font-bold">Nova Version</td>
                                <td class="py-2">{{ $novaVersion }}</td>
                            </tr>
                        @endif

                        <tr>
                            <td class="font-bold pt-2" colspan="2">Composer</td>
                        </tr>

                        <tr>
                            <td class="pb-2" colspan="2">
                                <a
                                    href="https://packagist.org/packages/{{ $package->composer_name }}"
                                    class="text-indigo-600 underline"
                                >{{ $package->composer_name }}</a>
                            </td>
                        </tr>

                        <tr>
                            <td class="font-bold py-2">GitHub stars</td>
                            <td class="py-2">{{ $package->github_stars }}</td>
                        </tr>

                        <tr>
                            <td class="font-bold py-2">Packagist downloads</td>
                            <td class="py-2">{{ number_format($package->packagist_downloads) }}</td>
                        </tr>
                    @endif
                </table>

                @if (! $composerLatest)
                    @if ($packagistData)
                        <p class="mb-2">
                            This package is listed on
                            <a href="https://packagist.org/packages/{{ $package->composer_name }}.json">the Packagist API</a>,
                            but has no stable tags.
                        </p>
                    @else
                        <p class="mb-2">
                            This package should be on
                            <a href="https://packagist.org/packages/{{ $package->composer_name }}.json">the Packagist API</a>
                            but we're not getting any results.
                        </p>
                    @endif

                    <p class="mb-2">
                        Please note that the Packagist cache is pretty long,
                        so some times you just need to check back in an hour.
                    </p>

                    @if ($currentUserOwns && !$refreshRequested)
                        <a
                            href="#"
                            wire:click.prevent="requestPackagistRefresh"
                            class="block mt-8 mb-2 underline"
                        >
                            Request a cache refresh from Packagist (the cache lasts 5 minutes)
                        </a>
                    @endif

                    @if ($refreshRequested)
                        <span class="block mt-8 mb-2">Refresh requested</span>
                    @endif
                @endif

                <div>
                    @if ($currentUserOwns && !$repositoryRefreshRequested)
                        <a
                            href="#"
                            wire:click.prevent="requestRepositoryRefresh"
                            class="block mt-8 mb-2 underline"
                        >
                            Request a refresh of the readme from your package registry or VCS provider.
                        </a>
                    @endif

                    @if ($repositoryRefreshRequested)
                        <span class="block mt-8 mb-2">Refresh requested</span>
                    @endif
                </div>
            </div>

            @if ($package->url)
                <div
                    class="p-4 md:p-6 border-solid border-gray-300 border-b overflow-hidden"
                    style="text-overflow: ellipsis; white-space: nowrap;"
                >
                    <h3 class="uppercase text-gray-600 text-sm pb-2 font-bold">URL</h3>
                    <a href="{{ $package->url }}" class="text-indigo-600 underline">{{ $package->url }}</a>
                </div>
            @endif

            <div class="p-4 pb-0 md:p-6 md:pb-2 border-gray-300 border-b">
                <h3 class="uppercase text-gray-600 text-sm font-bold">Favorites</h3>

                <div class="block py-4">
                    {{ $favoritesCount }} user{{ $favoritesCount == 1 ? '' : 's' }} favorited
                </div>

                @auth
                    <a
                        wire:click="toggleFavorite"
                        class="block text-indigo-600 no-underline font-bold text-sm cursor-pointer pb-4"
                    >
                        {{ $isFavorite ? 'Remove Favorite' : 'Add to Favorites' }}
                    </a>
                @endauth
            </div>

            @if (! $creatingReview)
                <div class="p-4 md:p-6 pb-4 border-gray-300 border-b">
                    <h3 class="uppercase text-gray-600 text-sm font-bold">Rating</h3>

                    @if (! $rated)
                        <div class="flex">
                            <div class="mt-2 mb-4 text-5xl w-1/2">
                                {{ $averageRating > 0 ? number_format($averageRating, 2) : 'N/A' }}
                            </div>
                            <div class="w-1/2 mb-6 text-gray-500 self-end">
                                (out of 5)
                            </div>
                        </div>
                    @else
                        <div class="mt-2 mb-4">
                            Thanks for rating this package!
                        </div>
                    @endif

                    @if (auth()->check() && !$isSelfAuthored && !$isSelfContributed)
                        <div class="mb-4 flex" x-data="{ hovered: 0 }">
                            <div class="w-1/3 pt-1 text-gray-600">
                                Tap to rate:
                            </div>
                            <div class="w-2/3 pl-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span
                                        class="cursor-pointer text-xl"
                                        :class="(hovered >= {{ $i }} || (hovered === 0 && {{ $currentUserRating }} >= {{ $i }})) ? 'text-yellow-500' : 'text-gray-300'"
                                        @mouseenter="hovered = {{ $i }}"
                                        @mouseleave="hovered = 0"
                                        @click="$wire.setRating({{ $i }})"
                                    >&#9733;</span>
                                @endfor
                            </div>
                        </div>
                    @endif

                    @foreach ($ratingCounts as $ratingCount)
                        <x-rating-count-bar
                            :stars="$ratingCount['number']"
                            :count="$ratingCount['count']"
                            :total-count="$totalRatings"
                        />
                    @endforeach

                    <div class="text-right text-sm text-gray-600 mt-2 mb-2">
                        {{ $totalRatings }} ratings
                    </div>

                    @if (auth()->check() && $currentUserReview->isEmpty() && !$isSelfAuthored && !$isSelfContributed)
                        <a
                            class="block text-indigo-600 no-underline font-bold text-sm cursor-pointer pb-4"
                            href="{{ route('reviews.create', [
                                'namespace' => $package->composer_vendor,
                                'name' => $package->composer_package,
                            ]) }}"
                        >
                            Review This Package
                        </a>
                    @endif
                </div>
            @endif

            <div class="p-4 md:p-6 border-gray-300 border-b">
                <h3 class="uppercase text-gray-600 text-sm font-bold">Author</h3>

                <div class="flex text-sm pt-4 items-center">
                    <img
                        src="{{ $package->author->avatar ?: 'https://api.adorable.io/avatars/285/' . Str::slug($package->author->name) . '.png' }}"
                        class="rounded-full h-10 w-10 mr-4"
                        alt="{{ $package->author->name }}"
                    />

                    <a
                        href="{{ route('collaborators.show', $package->author) }}"
                        class="text-indigo-600 font-bold no-underline uppercase text-sm hover:text-indigo-700"
                    >
                        {{ $package->author->name }}
                    </a>
                </div>
            </div>

            @if ($package->contributors->count())
                <div class="p-4 pb-2 md:p-6 border-gray-300 border-b">
                    <h3 class="uppercase text-gray-600 text-sm font-bold">Contributors</h3>

                    @foreach ($package->contributors as $contributor)
                        <div class="flex text-sm pt-4 items-center">
                            @include('livewire.partials.title-icon', [
                                'title' => $contributor->name,
                                'size' => 'medium',
                                'color' => '#606f7b',
                            ])

                            <a class="text-indigo-600 font-bold no-underline uppercase text-sm hover:text-indigo-700">
                                {{ $contributor->name }}
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="p-4 pb-0 md:p-6 md:pb-2 border-gray-300 border-b">
                <h3 class="uppercase text-gray-600 text-sm font-bold">Tags</h3>

                <div class="block py-4">
                    @foreach ($package->tags as $tag)
                        <a
                            href="{{ $tag->url() }}"
                            class="bg-indigo-200 text-indigo-600 rounded-l-full rounded-r-full px-4 py-2 mr-2 mb-2 inline-block font-bold no-underline"
                        >
                            {{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
