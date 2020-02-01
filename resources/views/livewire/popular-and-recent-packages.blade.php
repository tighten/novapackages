@extends('livewire.layouts.home-page')

@section('home-page-body')
<div class="w-full">
    <h2 class="ml-2 mb-2">Recent</h2>
    <div class="flex flex-wrap justify-center sm:justify-start">
        @foreach ($recentPackages as $package)
            @include('livewire.partials.package-card', ['context' => 'recent'])
        @endforeach
    </div>
    <a href="#" wire:click.prevent="filterTag('all')" class="font-bold ml-2 mb-6">See More...</a>

    <h2 class="ml-2 mb-2 mt-8">Popular</h2>
    <div class="flex flex-wrap justify-center sm:justify-start">
        @foreach ($popularPackages as $package)
            @include('livewire.partials.package-card', ['context' => 'popular'])
        @endforeach
    </div>
</div>
@endsection
