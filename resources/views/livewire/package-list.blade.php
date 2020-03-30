@extends('livewire.layouts.home-page')

@section('home-page-body')
<div class="w-full">
    @if ($tag === 'all')
        <h2 class="ml-2 mb-2 text-2xl font-bold">All Packages (newest first)</h2>
    @else
        <h2 class="ml-2 mb-6 text-2xl font-bold">Tag: {{ $tag }}</h2>
    @endif
    @if ($search)
        <div class="ml-2 mb-2">Filtered by search query "{{ $search }}"</div>
    @endif

    @if (! $packages->isEmpty())
        <div wire:key="package-bounding-box" class="flex flex-wrap justify-center sm:justify-start mb-6">
            @each('livewire.partials.package-card', $packages, 'package')
        </div>
    @else
        <div class="block w-full font-bold text-xl text-gray-800 self-start ml-2 mt-8">
            Sorry, but no packages currently in our database match this filter.
        </div>
    @endif

    @if ($packages->hasPages())
    <div class="md:flex mb-12">
        <div class="flex-initial mr-8">
            {!! $packages->links() !!}
        </div>
        <div x-data="{ 'pageSize': {{ $pageSize }}, 'open': false }" class="inline-block md:flex-initial ml-4 md:ml-0 mt-4 md:mt-0 relative text-gray-700">
            <span class="inline-block mr-2">Rows per page:</span>
            <div class="hover:bg-white inline-block border border-gray-400 cursor-pointer p-2 rounded-sm" x-on:click="open = true" :class="{ 'bg-white': open}">
                <span class="inline-block" x-text="pageSize"></span>
                <svg viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="inline-block fill-current text-gray-700 h-4 w-4">
                    <g stroke="none" stroke-width="1" fill-rule="evenodd">
                        <g>
                            <polygon points="9.29289322 12.9497475 10 13.6568542 15.6568542 8 14.2426407 6.58578644 10 10.8284271 5.75735931 6.58578644 4.34314575 8"></polygon>
                        </g>
                    </g>
                </svg>
            </div>

            <div x-show="open" x-on:click.away="open = false" class="absolute bg-white border border-gray-400 mt-9 top-0 w-full" x-cloak>
                @foreach ([6, 12, 18, 24] as $thisPageSize)
                <a href="#"
                    class="block {{ $pageSize == $thisPageSize ? 'font-bold' : '' }} hover:bg-gray-100 hover:text-gray-900 p-2 text-center"
                    wire:click.prevent="changePageSize({{ $thisPageSize }})">{{ $thisPageSize }}</a>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
    <script>
        document.onkeydown = function(e) {
            e = e || window.event;

            switch(e.which || e.keyCode) {
                case 37: // left
                    @this.call('previousPage');
                break;
                case 39: // right
                    @this.call('nextPage');
                break;

                default: return;
            }

            e.preventDefault();
        }
    </script>
@endpush
