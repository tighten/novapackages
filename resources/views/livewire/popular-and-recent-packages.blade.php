@extends('livewire.layouts.home-page')

@section('home-page-body')
<div class="w-full">
    <h2 class="ml-2 mb-2">Recent</h2>
    <div class="flex flex-wrap justify-center sm:justify-start">
        @each('livewire.partials.package-card', $recentPackages, 'package')
}
    </div>
    <a href="#" @click.prevent="tag = 'all'" class="font-bold ml-2 mb-6">See More...</a>

    <h2 class="ml-2 mb-2 mt-8">Popular</h2>
    <div class="flex flex-wrap justify-center sm:justify-start">
        @each('livewire.partials.package-card', $popularPackages, 'package')
    </div>
</div>
@endsection
