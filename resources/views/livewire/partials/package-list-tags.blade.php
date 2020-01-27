<div class="block w-full sm:hidden relative mx-auto mb-4 px-2" style="max-width: 380px;">
            @todo handle nav on mobile
    {{--
                <select v-model="tag" @change="filterTag($event.target.value)" class="block appearance-none w-full bg-white border border-grey-light hover:border-grey px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                    <option value="popular---and---recent">
                        Popular &amp; Recent
                    </option>

                    <option value="all">
                        All Packages
                    </option>

                    <optgroup label="Package types">
                        <option v-for="(thisTag) in typeTags" v-bind:key="thisTag.slug" :value="thisTag.slug">
                            {{ thisTag.name }}
                        </option>
                    </optgroup>
                    <optgroup label="Popular tags">
                        <option v-for="(thisTag) in popularTags" v-bind:key="thisTag.slug" :value="thisTag.slug">
                            {{ thisTag.name }}
                        </option>
                    </optgroup>
                </select>
--}}
                <div class="pointer-events-none absolute pin-y pin-r flex items-center px-2 mr-2 text-grey-darker">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" /></svg>
                </div>
            </div>



    <div class="hidden sm:block flex-no-shrink flex-no-grow mr-4" style="min-width: 13em;">
        <nav>
            <a
                wire:click="filterTag('popular--and--recent')"
                class="block px-8 py-2 cursor-pointer hover:text-indigo-dark {{ $tag === 'popular--and--recent' ? 'text-grey-darkest font-bold' : 'text-grey-darker' }}"
                >Popular and recent</a><br>

            <a
                wire:click="filterTag('all')"
                class="block px-8 py-2 cursor-pointer hover:text-indigo-dark {{ $tag === 'all' ? 'text-grey-darkest font-bold' : 'text-grey-darker' }}"
                >All packages</a>

            <span class="block mt-4 mb-2 mx-4 pb-2 px-4 mt-6 border-b border-grey uppercase text-sm">Package types</span>

            @foreach ($typeTags as $thisTag)
            <a
                wire:click="filterTag('{{ $thisTag->slug }}')"
                class="block px-8 py-2 cursor-pointer hover:text-indigo-dark {{ $thisTag->slug === $tag ? 'text-grey-darkest font-bold' : 'text-grey-darker' }}"
                >{{ $thisTag->name }}</a>
            @endforeach

            <span class="block mt-6 mb-2 mx-4 pb-2 px-4 mt-4 border-b border-grey uppercase text-sm">Popular tags</span>
            @foreach ($popularTags as $thisTag)
            <a
                wire:click="filterTag('{{ $thisTag->slug }}')"
                class="block px-8 py-2 cursor-pointer hover:text-indigo-dark {{ $thisTag->slug === $tag ? 'text-grey-darkest font-bold' : 'text-grey-darker' }}"
                >{{ $thisTag->name }}</a>
            @endforeach
        </nav>
    </div>
