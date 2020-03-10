@extends('layouts.app')

@section('body')
@php
// fake for now, @todo get from Vue computed props
$rated = false;
$totalRatings = 42;
@endphp
<div class="p-4 md:p-6 pb-4 border-gray-300 border-b">
    <h3 class="uppercase text-gray-600 text-sm font-bold">Rating</h3>

    @if ($rated)
        <div class="mt-2 mb-4">
            Thanks for rating this package!
        </div>
    @else
        <div class="flex">
            <div class="mt-2 mb-4 text-5xl w-1/2">
                {{ $package->averageRating() ?: 'None yet' }}
            </div>

            <div class="w-1/2 mb-6 text-gray-500 self-end">
                (out of 5)
            </div>
        </div>
    @endif

    @if (auth()->check() && auth()->user()->can('rate', $package))
        <div class="mb-4 flex">
            <div class="w-1/3 pt-1 text-gray-600">
                Tap to rate:
            </div>

            <div class="w-2/3 pl-2">
                <star-rating
                    rating="{{ $package->current_user_rating }}"
                    :read-only="!auth"
                    :star-size="20"
                    :show-rating="false"
                    @rating-selected="setRating"
                ></star-rating>
            </div>
        </div>
    @endif

    <rating-count-bar
        :totalCount="totalRatings"
        :stars="rating_count.number"
        :count="rating_count.count"
        :key="package.id + 'rate' + rating_count.number"
        v-for="rating_count in package.rating_counts"
    />

    <div class="text-right text-sm text-gray-600 mt-2 mb-2">
        {{ $totalRatings }} ratings
    </div>

    @auth
        @if (auth()->user()->can('rate', $package) && ! auth()->user()->hasReviewed($package))
            <div>
                <a
                    class="block text-indigo-600 no-underline font-bold text-sm cursor-pointer pb-4"
                    href="{{ route('reviews.create', [
                        'namespace' => $package->composer_vendor,
                        'name' => $package->composer_package,
                    ]) }}"
                >
                    Review This Package
                </a>
            </div>
        @endif
    @endauth
</div>

@endsection
