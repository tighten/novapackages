@php
$package = (new App\Http\Resources\PackageResource($package))->toArray($package);
$package['accent'] = app(App\Colors::class)->nextColor();
@endphp
<div class="flex m-2 mb-4 shadow hover:shadow-md h-128 w-full max-w-xs rounded" wire:key="{{ $context ?? 'no-context' }}-{{ $package['id'] }}">
    <div style="border: 1px solid #ddd; border-top-width: 4px; border-top-color: {{ $package['accent'] }}" class="flex-1 bg-white text-sm rounded">
        @if (optional(auth()->user())->isAdmin())
            <div class="text-right -mb-6">
                <div class="relative" x-data="{ open: false }">
                    <div role="button" class="inline-block select-none p-2" @click="open = !open">
                        <span class="appearance-none flex items-center inline-block text-white font-medium">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                            </svg>
                        </span>
                    </div>
                    <div class="absolute right-0 w-auto mr-2 z-50" x-show="open">
                        <div class="bg-indigo shadow rounded border overflow-hidden" x-cloak>
                            @if ($package['is_disabled'])
                                <a href="{{ route('app.admin.enable-package', $package['id']) }}" class="no-underline block px-4 py-3 border-b text-white bg-indigo-500 hover:text-white hover:bg-blue-500">
                                    Enable
                                </a>
                            @else
                                <a href="{{ route('app.admin.disable-package', $package['id']) }}" class="no-underline block px-4 py-3 border-b text-white bg-indigo-500 hover:text-white hover:bg-blue-500">
                                    Disable
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="flex flex-row mt-4 px-4 pb-4" style="height: 14em">
            <div class="pb-2 w-full relative">
                <a href="{{ route('packages.show', ['namespace' => $package['packagist_namespace'], 'name' => $package['packagist_name']]) }}" class="block mb-2 no-underline">
                    <h2 class="text-xl font-bold text-gray-800 flex flex-row items-center">
                        @include('livewire.partials.title-icon', [
                            'color' => $package['accent'],
                            'size' => 'small',
                            'title' => str_replace(['Laravel Nova ', 'Nova '], [], $package['name']),
                        ])
                        {{ str_replace(['Laravel Nova ', 'Nova '], [], $package['name']) }}

                        @if ($package['is_disabled'])
                            <span class="text-xs uppercase text-gray-400">Disabled</span>
                        @endif
                    </h2>
                </a>

                <div class="flex flex-row absolute bottom-0 right-0">
                    <div class="flex">
                        @include('partials.stars', ['stars' => $package['average_rating']])
                    </div>

                    <div class="flex text-gray-500 pt-1 pl-1 text-xs">
                        ({{ $package['rating_count'] }})
                    </div>
                </div>

                <div class="text-gray-800 leading-normal mb-4 markdown leading-tight w-full" style="word-break: break-word;">
                    {!! $package['abstract'] !!}
                </div>

                <a href="{{ route('packages.show', ['namespace' => $package['packagist_namespace'], 'name' => $package['packagist_name']]) }}" class="absolute block text-indigo-600 font-bold no-underline bottom-0 left-0">
                    Learn More
                </a>
            </div>
        </div>

        <div class="bg-gray-100 flex text-sm border-t border-gray-300 px-4 py-4 items-center rounded-b">
            <img src="{{ $package['author']['avatar_url'] }}" class="rounded-full h-6 w-6 mr-4" alt="{{ $package['author']['name'] }}" />

            <a href="/collaborators/{{ $package['author']['github_username'] }}" class="text-indigo-600 font-bold no-underline uppercase text-xs hover:text-indigo-700">
                {{ $package['author']['name'] }}
            </a>
        </div>
    </div>
</div>
