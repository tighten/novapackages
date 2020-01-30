@extends('livewire.layouts.home-page')

@section('home-page-body')
<div class="w-full">
    @if ($tag === 'all')
        <h2 class="ml-2 mb-2">All Packages (newest first)</h2>
    @else
        <h2 class="ml-2 mb-6">Tag: {{ $tag }}</h2>
    @endif
    @if ($search)
        <div class="ml-2 mb-2">Filtered by search query "{{ $search }}"</div>
    @endif

    @if (! $packages->isEmpty())
        <div class="flex flex-wrap justify-center sm:justify-start mb-6">
            @each('livewire.partials.package-card', $packages, 'package')
        </div>
    @else
        <div class="block w-full font-bold text-xl text-grey-darkest self-start ml-2 mt-8">
            Sorry, but no packages currently in our database match this filter.
        </div>
    @endif

    {!! $packages->links() !!}
</div>
@endsection
