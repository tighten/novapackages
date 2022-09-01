<div class="p-4 pb-0 md:p-6 md:pb-2 border-gray-300 border-b">
    <h3 class="uppercase text-gray-600 text-sm font-bold">Favorites</h3>

    <div class="block py-4">
        {{ $favoriteCount }} {{ str_plural('user', $favoriteCount) }} favorited
    </div>

    @auth
        <span
            wire:click="toggleFavorite"
            class="block text-indigo-600 no-underline font-bold text-sm cursor-pointer pb-4"
        >
            @if ($isFavorite)
                Remove Favorite
            @else
                Add to Favorites
            @endif
        </span>
    @endauth
</div>
