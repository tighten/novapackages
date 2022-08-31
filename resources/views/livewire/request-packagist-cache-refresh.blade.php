<div>
    @if ($refreshRequested)
        <span class="block mt-8 mb-2">
            Refresh requested
        </span>
    @else
        <span
            wire:click="requestRefresh"
            class="block mt-8 mb-2 cursor-pointer"
        >
            Request a cache refresh from Packagist (the cache lasts 5 minutes)
        </span>
    @endif
</div>
