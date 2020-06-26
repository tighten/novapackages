@extends('layouts.app')

@section('title', 'Package Ideas')
@section('meta')
    @og('title', 'Package Ideas for Laravel Nova')
    @og('type', 'website')
    @og('url', route('package-ideas'))
    @og('image', url('images/package-opengraph-fallback.png'))
    @og('description', 'Package ideas, from the community, for anyone looking to get started making packages for Laravel Nova.')
    @og('site_name', 'Nova Packages')
    <meta name="twitter:card" content="summary_large_image">
@endsection

@section('content')
<div class="flex items-center">
    <div class="md:w-1/2 mx-4 md:mx-auto">
        <div class="font-bold text-2xl py-3">
            Package Ideas
        </div>
        <div class="rounded shadow">
            <div class="bg-white p-3 md:p-6 rounded-b leading-normal">
                <p class="mb-4">Looking to get started developing packages for the Nova community? Here are some suggestions for you to choose from.</p>
                <h2 class="text-lg font-bold">How does this work?</h2>
                <p class="mb-4">These package ideas can be <a href="https://github.com/tightenco/nova-package-development/issues/new">added on GitHub</a> by adding the <code>package-idea</code> label to any issue. Anyone can "claim" an idea by leaving a comment "staking" your claim (and then the author or someone at Tighten will add the <code>challenge-accepted</code> label); it doesn't give you magical rights to it, but it is a signal that you intend to work on it so two people don't accidentally both work on the same package idea.</p>

                <p class="mb-4">Launch your package? Put a link in a comment so the original author (or someone from Tighten) can close the issue.</p>

                <h2 class="mb-2 mt-8 text-xl font-bold">Un-claimed ideas</h2>
                @each('partials.package-idea', $unclaimed_ideas, 'idea')

                <h2 class="mb-2 mt-8 text-xl font-bold">Claimed but not completed ideas</h2>
                @each('partials.package-idea', $claimed_ideas, 'idea') </div>
        </div>
    </div>
</div>
@endsection

