<div>
    <div class="flex flex-col container mx-auto pb-8">
        <p class="block text-center text-3xl sm:text-4xl mb-10 mx-4 text-grey-darkest">Discover new packages. <br class="md:hidden block" />Build amazing things.</p>

        TODO: package search<br><br>
        Also todo: De-duplciate this with package-list.blade.php<br><br>

        <!-- Flexes the left/right column of the whole page layout -->
        <div class="flex flex-col sm:flex-row justify-around mt-8">
            @include('livewire.partials.package-list-tags')

            <!-- The body -->
            <div class="w-full">
                <!-- Popular and Recent -->
                <div class="w-full">
                    <h2 class="ml-2 mb-2">Recent</h2>
                    <div class="flex flex-wrap justify-center sm:justify-start">
                        @each('livewire.partials.package-card', $recentPackages, 'package')
}
                    </div>
                    <a href="#" @click.prevent="tag = 'all'" class="font-bold ml-2 mb-6">See More...</a>

                    <h2 class="ml-2 mb-2 mt-8">Popular</h2>
                    <div class="flex flex-wrap justify-center sm:justify-start">
                        {{--
                        <package-card
                            :key="thisPackage.id"
                            v-for="thisPackage in popularPackages"
                            :package="thisPackage"
                        ></package-card>
                        --}}
                        @each('livewire.partials.package-card', $popularPackages, 'package')
                    </div>

                    <div wire:loading class="text-4xl text-center text-grey tracking-wide py-8 my-8">LOADING...</div>
                </div>
            </div>
        </div>
    </div>
</div>
