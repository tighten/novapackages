@php
// hack shit
$package = (new App\Http\Resources\PackageResource($package))->toArray($package);
// @todo
$package['accent'] = Arr::random([
    '#6574cd', // Blue
    '#41ac9c', // Teal
    '#e49334', // Orange
    '#56ad34', // Green
    '#c34949', // Red
    '#a72b9d', // Purple
    '#d2c823', // Yellow
]);
@endphp
<div class="flex m-2 mb-4 shadow hover:shadow-md h-128 w-full max-w-xs">
    <div style="border: 1px solid #ddd; border-top-width: 4px; border-top-color: {{ $package['accent'] }}" class="flex-1 bg-white text-sm rounded-sm">
        @if (optional(auth()->user())->isAdmin())
        <div class="text-right -mb-6">
            <admin-dropdown>
                <span slot="link" class="appearance-none flex items-center inline-block text-white font-medium">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                    </svg>
                </span>

                <div slot="dropdown" class="bg-indigo shadow rounded border overflow-hidden">
                    <a v-if="package.is_disabled == true" :href="route('app.admin.enable-package', package)" class="no-underline block px-4 py-3 border-b text-white bg-indigo hover:text-white hover:bg-blue">
                        Enable
                    </a>

                    <a v-else :href="route('app.admin.disable-package', package)" class="no-underline block px-4 py-3 border-b text-white bg-indigo hover:text-white hover:bg-blue">
                        Disable
                    </a>
                </div>
            </admin-dropdown>
        </div>
        @endif

        <div class="flex flex-row mt-4 px-4 pb-4" style="height: 14em">
            <div class="pb-2 w-full relative">
                <a href="{{ route('packages.show', ['namespace' => $package['packagist_namespace'], 'name' => $package['packagist_name']]) }}" class="block mb-2 no-underline">
                    <h2 class="text-xl text-grey-darkest flex flex-row items-center">
                        @include('livewire.partials.title-icon', [
                            'color' => $package['accent'],
                            'size' => 'small',
                            'title' => $package['name'],
                        ])
                        @todo make name sans-nova (regex)<br>
                        {{ $package['name'] }}

                        @if ($package['is_disabled'])
                        <span class="text-xs uppercase text-grey-light">Disabled</span>
                        @endif
                    </h2>
                </a>

                <div class="flex flex-row absolute pin-b pin-r">
                    <div class="flex">
                        @include('partials.stars', ['stars' => $package['average_rating']])
                    </div>

                    <div class="flex text-grey-dark pt-1 pl-1 text-xs">
                        ({{ $package['rating_count'] }})
                    </div>
                </div>

                <div class="text-grey-darkest leading-normal mb-4 markdown leading-tight w-full" style="word-break: break-word;">
                    {!! $package['abstract'] !!}
                </div>

                <a href="{{ route('packages.show', ['namespace' => $package['packagist_namespace'], 'name' => $package['packagist_name']]) }}" class="absolute block text-indigo font-bold no-underline pin-b pin-l">
                    Learn More
                </a>
            </div>
        </div>

        <div class="bg-grey-lightest flex text-sm border-t border-grey-light px-4 py-4 items-center">
            <img src="{{ $package['author']['avatar_url'] }}" class="rounded-full h-6 w-6 mr-4" alt="{{ $package['author']['name'] }}" />

            <a href="/collaborators/{{ $package['author']['github_username'] }}" class="text-indigo font-bold no-underline uppercase text-xs hover:text-indigo-dark">
                {{ $package['author']['name'] }}
            </a>
        </div>
    </div>
</div>
