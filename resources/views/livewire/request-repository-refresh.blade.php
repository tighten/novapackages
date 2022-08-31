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
            Request a refresh of the readme from your package registry or VCS provider.
        </span>
    @endif
</div>
