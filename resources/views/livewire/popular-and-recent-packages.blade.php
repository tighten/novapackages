@extends('livewire.layouts.home-page')

@section('home-page-body')
<div class="w-full">
    <h2 class="ml-2 mb-2 text-2xl font-bold">Recent</h2>
    <div class="flex flex-wrap justify-center sm:justify-start">
        @foreach ($recentPackages as $package)
            @include('livewire.partials.package-card', ['context' => 'recent'])
        @endforeach
    </div>
    <a href="#" wire:click.prevent="filterTag('all')" class="text-indigo-600 underline font-bold ml-2 mb-6">See More...</a>

    <h2 class="ml-2 mb-2 mt-8 text-2xl font-bold">Popular</h2>
    <div class="flex flex-wrap justify-center sm:justify-start">
        @foreach ($popularPackages as $package)
            @include('livewire.partials.package-card', ['context' => 'popular'])
        @endforeach

        {!! $popularPackages->links() !!}
    </div>
</div>
@endsection
