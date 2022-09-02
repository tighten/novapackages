@if (! $isCreatingReview)
    <div class="p-4 md:p-6 pb-4 border-gray-300 border-b">
        <h3 class="uppercase text-gray-600 text-sm font-bold">Rating</h3>

        @if (! $userHasRated)
            <div class="flex">
                <div @class(['mt-2 mb-4 w-1/2', 'text-5xl' => $averageRating])>
                    @if ($averageRating) {{ number_format($averageRating, 2) }} @else None yet @endif
                </div>

                <div class="w-1/2 mb-6 text-gray-500 self-end">
                    @if ($averageRating) (out of 5) @endif
                </div>
            </div>
        @else
            <div class="mt-2 mb-4">
                Thanks for rating this package!
            </div>
        @endif

        @if (auth()->check() && ! $isSelfAuthored && ! $isSelfContributed)
            <div class="mb-4 flex">
                <div class="w-1/3 pt-1 text-gray-600">
                    Tap to rate:
                </div>

                <div class="w-2/3 pl-2">
                    {{--<star-rating--}}
                    {{--    v-model="package.current_user_rating"--}}
                    {{--    :rating="package.current_user_rating"--}}
                    {{--    :read-only="!auth"--}}
                    {{--    :star-size="20"--}}
                    {{--    :show-rating="false"--}}
                    {{--    @rating-selected="setRating"--}}
                    {{--></star-rating>--}}
                </div>
            </div>
        @endif

        {{--<rating-count-bar--}}
        {{--    :totalCount="totalRatings"--}}
        {{--    :stars="rating_count.number"--}}
        {{--    :count="rating_count.count"--}}
        {{--    :key="package.id + 'rate' + rating_count.number"--}}
        {{--    v-for="rating_count in package.rating_counts"--}}
        {{--/>--}}

        <div class="text-right text-sm text-gray-600 mt-2 mb-2">
            {{ $ratingCount }} {{ str_plural('rating', $ratingCount) }}
        </div>

        @if(auth()->check() && ! $userHasReviewed && ! $isSelfAuthored && ! $isSelfContributed)
            <div>
                <a
                    class="block text-indigo-600 no-underline font-bold text-sm cursor-pointer pb-4"
                    href="{{ $reviewUrl }}"
                >
                    Review This Package
                </a>
            </div>
        @endif
    </div>
@endif
