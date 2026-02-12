<div class="relative">
    <div class="flex flex-col container mx-auto pb-8">
        <p class="block text-center text-3xl sm:text-4xl mb-10 mx-4 text-gray-900">Discover new packages. <br class="md:hidden block" />Build amazing things.</p>

        @include('livewire.partials.search')

        <!-- Flexes the left/right column of the whole page layout -->
        <div class="flex flex-col sm:flex-row justify-around sm:mt-8">
            @include('livewire.partials.package-list-tags')

            <div class="w-full">
                <h2 class="ml-2 mb-2 text-2xl font-bold">Recent</h2>
                <div class="flex flex-wrap justify-center sm:justify-start">
                    @foreach ($recentPackages as $package)
                        @include('livewire.partials.package-card', ['context' => 'recent'])
                    @endforeach
                </div>
                <a href="#" wire:click.prevent="filterTag('all')" class="text-indigo-600 underline font-bold ml-2 mb-6">See More...</a>

                <h2 class="ml-2 mb-2 mt-8 text-2xl font-bold">Popular</h2>
                <div class="flex flex-wrap justify-center sm:justify-start">
                    @foreach ($popularPackages as $package)
                        @include('livewire.partials.package-card', ['context' => 'popular'])
                    @endforeach
                </div>
                <a href="#" wire:click.prevent="filterTag('popular')" class="text-indigo-600 underline font-bold ml-2 mb-6">See More...</a>
            </div>
        </div>
    </div>
    <div wire:loading style="top: 7em; width: 10em; left: 50%; margin-left: -5em" class="absolute bg-indigo-900 rounded-lg text-4xl text-center text-white tracking-wide py-6 w-full uppercase w-4">Loading...</div>
</div>
