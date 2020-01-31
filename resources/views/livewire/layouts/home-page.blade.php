<div>
    <div class="flex flex-col container mx-auto pb-8">
        <p class="block text-center text-3xl sm:text-4xl mb-10 mx-4 text-grey-darkest">Discover new packages. <br class="md:hidden block" />Build amazing things.</p>

        @include('livewire.partials.search')

        <!-- Flexes the left/right column of the whole page layout -->
        <div class="flex flex-col sm:flex-row justify-around sm:mt-8">
            @include('livewire.partials.package-list-tags')

            <!-- The body -->
            <div class="w-full relative">
                @yield('home-page-body')

                {{-- @todo clean this UI up --}}
                <div wire:loading style="top: 3em" class="bg-black absolute text-4xl text-center text-grey tracking-wide mx-12 py-8 w-full uppercase w-4">Loading...</div>
            </div>
        </div>
    </div>
</div>
