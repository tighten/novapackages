<div>
    <div class="flex flex-col container mx-auto pb-8">
        <p class="block text-center text-3xl sm:text-4xl mb-10 mx-4 text-grey-darkest">Discover new packages. <br class="md:hidden block" />Build amazing things.</p>

        <input wire:model="search" placeholder="Search" class="p-4">

        <!-- Flexes the left/right column of the whole page layout -->
        <div class="flex flex-col sm:flex-row justify-around mt-8">
            @include('livewire.partials.package-list-tags')

            <!-- The body -->
            <div class="w-full">
                <!-- All packages, or packages by type -->
                <div class="w-full" v-show="tag !== 'popular---and---recent'">
                    @if ($tag === 'all')
                    <h2 class="ml-2 mb-2">All Packages (newest first)</h2>
                    @else
                    <h2 class="ml-2 mb-6">Tag: {{ $tag }}</h2>
                    @endif
                    @if ($search)
                        Filtered by search query "{{ $search }}"
                    @endif

                    @if (! $packages->isEmpty())
                    <div class="flex flex-wrap justify-center sm:justify-start">
                        @each('livewire.partials.package-card', $packages, 'package')
                    </div>
                    @else
                    <div class="block w-full font-bold text-xl text-grey-dark self-start ml-2">
                        Sorry, but no packages currently in our database match this filter.
                    </div>
                    @endif

                    Total records: {{ $packageCount }}<br>
                    Page {{ $page }} / {{ ceil($packageCount / $perPage) }}<br>

                    <button wire:click="prevPage">Previous page</button>
                    <button wire:click="nextPage">Next page</button>

                    <div wire:loading class="text-4xl text-center text-grey tracking-wide py-8 my-8">LOADING...</div>
                </div>
            </div>
        </div>
    </div>
</div>
