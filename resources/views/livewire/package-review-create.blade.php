<div class="m-4 md:m-10" x-data="{ contentLength: 0 }">
    <h1 class="text-lg text-indigo-700 font-normal pt-2 pb-6">
        To submit a review, select a star rating and enter your comments below.
    </h1>

    <div class="container mb-4 flex">
        <div class="pt-1 text-gray-600">Your Rating:</div>

        <div class="w-2/3 pl-2 flex-row" x-data="{ hovered: 0 }">
            @for($i = 1; $i <= 5; $i++)
                <span
                    class="cursor-pointer text-2xl"
                    :class="(hovered >= {{ $i }} || (hovered === 0 && {{ $rating }} >= {{ $i }})) ? 'text-yellow-500' : 'text-gray-300'"
                    @mouseenter="hovered = {{ $i }}"
                    @mouseleave="hovered = 0"
                    @click="$wire.setRating({{ $i }})"
                >&#9733;</span>
            @endfor
        </div>
    </div>

    <textarea
        wire:model="content"
        class="w-full mb-1 p-2 border border-indigo-700"
        autofocus
        maxlength="5000"
        minlength="20"
        placeholder="Write Your Review Here"
        required
        rows="5"
        x-on:input="contentLength = $event.target.value.length"
    ></textarea>

    @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

    <button
        wire:click="submit"
        class="w-full md:w-auto text-white rounded-xs no-underline flex justify-center items-center mt-2 py-4 px-4 sm:px-6"
        :class="$wire.rated && contentLength >= 20
            ? 'bg-indigo-600 hover:bg-indigo-700 cursor-pointer'
            : 'bg-gray-400 cursor-not-allowed'"
        :disabled="! $wire.rated || contentLength < 20"
    >
        Submit Review
    </button>
</div>
