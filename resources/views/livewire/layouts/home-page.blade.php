<div>
    <div class="flex flex-col container mx-auto pb-8">
        <p class="block text-center text-3xl sm:text-4xl mb-10 mx-4 text-grey-darkest">Discover new packages. <br class="md:hidden block" />Build amazing things.</p>

        @include('livewire.partials.search')

        <!-- Flexes the left/right column of the whole page layout -->
        <div class="flex flex-col sm:flex-row justify-around mt-8">
            @include('livewire.partials.package-list-tags')

            <!-- The body -->
            <div class="w-full">
                @yield('home-page-body')

                <div wire:loading class="text-4xl text-center text-grey tracking-wide py-8 my-8">LOADING...</div>
            </div>
        </div>
    </div>
</div>
