<div
    class="container leading-tight py-4 w-full"
    x-data="{ isEditing: @entangle('isEditing') }"
>
    <div>
        <livewire:star-rating :rating="$review->rating->rating" :read-only="true"/>
    </div>
    <div x-show="! isEditing">
        <p class="text-gray-600 text-sm py-1">
            By {{ $review->user->name }} on {{ $review->updated_at->format('M d, Y') }}
        </p>
        <p class="text-gray-800 py-1">{{ $review->content }}</p>
    </div>
    @if (auth()->check() && ($review->user->id === auth()->id() || auth()->user()->isAdmin()))
        <div
            class="flex mt-2"
            x-show="! isEditing"
        >
            <button
                class="cursor-pointer bg-indigo-600 text-white no-underline hover:bg-indigo-700 flex justify-center items-center py-2 px-4 md:px-6"
                x-on:click="isEditing = ! isEditing"
            >
                Edit
            </button>
            <form wire:submit.prevent="delete">
                <button
                    class="flex text-red no-underline font-bold text-md uppercase cursor-pointer ml-2 px-2"
                >
                    Delete
                </button>
            </form>
        </div>
        <div x-show="isEditing" x-cloak>
            <textarea
                class="w-full leading-tight mb-1 p-2 border border-indigo-700"
                wire:model.lazy="review.content"
                maxLength="5000"
                minlength="20"
                required
                rows="5"
            ></textarea>
            @error('review.content') <span class="error">{{ $message }}</span> @enderror
            <button
                class="flex justify-center items-center w-full md:w-auto cursor-pointer no-underline text-white bg-indigo-600 hover:bg-indigo-700 py-4 px-4 sm:px-6"
                type="button"
                wire:click="update"
            >
                Update Review
            </button>
        </div>
    @endif
</div>
