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
