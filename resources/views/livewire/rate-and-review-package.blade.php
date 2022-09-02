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
            <div class="mb-4 flex items-center">
                <span class="w-1/3 pt-1 text-gray-600">Tap to rate:</span>

                <div class="w-2/3 pl-2">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg
                            @class([
                                'inline h-5 w-5 cursor-pointer',
                                'fill-gray-300' => $currentUserRating === 0 || $currentUserRating <= $i,
                                'fill-yellow-500' => $currentUserRating >= $i,
                            ])
                            wire:click="rate({{ $i }})"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
            </div>
        @endif

        @foreach ($ratingCounts as $ratingLevel)
            <div class="flex my-1">
                <div class="w-1/3 text-xs pr-1 text-gray-500 text-right">{{ str_repeat('★', $ratingLevel['number']) }}</div>
                <div class="w-2/3 w-full bg-gray-200 h-2 mt-1">
                    <div
                        class="bg-yellow-500 h-2"
                        style="width: {{ $this->totalRatingCount === 0 ? 0 : round($ratingLevel['count'] / $this->totalRatingCount * 100) }}%"
                    ></div>
                </div>
            </div>
        @endforeach

        <div class="text-right text-sm text-gray-600 mt-2 mb-2">
            {{ $ratingCount }} {{ str_plural('rating', $ratingCount) }}
        </div>

        @if(auth()->check() && ! $isSelfAuthored && ! $isSelfContributed)
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
