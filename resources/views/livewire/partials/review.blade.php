@php
    $userRating = $review->rating?->rating ?? 0;
    $canEdit = auth()->check() && (auth()->id() == $review->user_id || auth()->user()->isAdmin());
@endphp

<div wire:key="review-{{ $review->id }}">
    @if ($editingReviewId === $review->id)
        {{-- Editing mode --}}
        <div class="container py-4">
            <div class="mb-4 flex">
                @include('livewire.partials.star-rating-display', ['rating' => $userRating])
            </div>

            <textarea
                wire:model="editingContent"
                class="w-full leading-tight mb-1 p-2 border border-indigo-700"
                maxlength="5000"
                minlength="20"
                required
                rows="5"
            ></textarea>

            <div class="flex gap-2">
                <button
                    wire:click="update({{ $review->id }})"
                    class="flex justify-center items-center w-full md:w-auto cursor-pointer no-underline text-white bg-indigo-600 hover:bg-indigo-700 py-4 px-4 sm:px-6"
                >
                    Update Review
                </button>
                <button
                    wire:click="cancelEdit"
                    class="flex justify-center items-center cursor-pointer text-gray-600 hover:text-gray-800 py-4 px-4"
                >
                    Cancel
                </button>
            </div>
        </div>
    @else
        {{-- Display mode --}}
        <div class="container leading-tight py-4 w-full">
            @include('livewire.partials.star-rating-display', ['rating' => $userRating])

            <p class="text-gray-600 text-sm py-1">By {{ $review->user->name }} on {{ $review->updated_at->format('F j, Y') }}</p>

            <p class="text-gray-800 py-1">{{ $review->content }}</p>

            @if ($canEdit)
                <div class="flex mt-2">
                    <button
                        wire:click="edit({{ $review->id }})"
                        class="cursor-pointer bg-indigo-600 text-white no-underline hover:bg-indigo-700 flex justify-center items-center py-2 px-4 md:px-6"
                    >
                        Edit
                    </button>

                    <button
                        wire:click="delete({{ $review->id }})"
                        wire:confirm="Are you sure you want to delete this review?"
                        class="flex text-red-600 no-underline font-bold text-base uppercase cursor-pointer ml-2 px-2"
                    >
                        Delete
                    </button>
                </div>
            @endif
        </div>
    @endif
</div>
