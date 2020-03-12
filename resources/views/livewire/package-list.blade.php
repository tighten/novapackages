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

    {!! $packages->links() !!}

    @if ($tag !== 'all')
        <div class="mt-4 text-gray-700">
            <span class="inline-block mr-2">Per page</span>
            @foreach ([6, 12, 18, 24] as $thisPageSize)
            <span class="text-gray-500">|</span>
            <a href="#"
                class="{{ $pageSize == $thisPageSize ? 'font-bold' : '' }} hover:text-gray-900 inline-block p-2"
                wire:click.prevent="changePageSize({{ $thisPageSize }})">{{ $thisPageSize }}</a>
            @endforeach
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
