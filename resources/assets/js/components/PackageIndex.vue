<template>
    <div class="flex flex-col container mx-auto pb-8">
        <p class="block text-center text-3xl sm:text-4xl mb-10 mx-4 text-grey-darkest">Discover new packages. <br class="md:hidden block" />Build amazing things.</p>

        <package-search></package-search>

        <!-- Flexes the left/right column of the whole page layout -->
        <div class="flex flex-col sm:flex-row justify-around mt-8">
            <div class="block w-full sm:hidden relative mx-auto mb-4 px-2" style="max-width: 380px;">
                <select v-model="tag" @change="filterTag($event.target.value)"
                    class="block appearance-none w-full bg-white border border-grey-light hover:border-grey px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
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
                <div class="pointer-events-none absolute pin-y pin-r flex items-center px-2 mr-2 text-grey-darker">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                </div>
            </div>

            <div class="hidden sm:block flex-no-shrink flex-no-grow mr-4" style="min-width: 13em;">
                <nav>
                    <a
                        :class="{'hover:text-indigo-dark block px-8 py-4 cursor-pointer': true, 'text-grey-darkest font-bold': tag == 'popular---and---recent', 'text-grey-darker': tag != 'popular---and---recent'}"
                        @click="filterTag('popular---and---recent')"
                    >
                        Popular &amp; Recent
                    </a>

                    <a
                        :class="{'hover:text-indigo-dark block px-8 py-4 cursor-pointer': true, 'text-grey-darkest font-bold': tag == 'all', 'text-grey-darker': tag != 'all'}"
                        @click="filterTag('all')"
                    >
                        All Packages
                    </a>

                    <span class="block mt-4 mb-2 mx-4 pb-2 px-4 mt-6 border-b border-grey uppercase text-sm">Package types</span>

                    <a
                        :key="thisTag.slug"
                        v-for="thisTag in typeTags"
                        class="block px-8 py-2 cursor-pointer hover:text-indigo-dark"
                        :class="{'text-grey-darkest font-bold': thisTag.slug === tag, 'text-grey-darker': thisTag.slug != tag, 'text-grey' : tagBySlugHasNoPackages(thisTag.slug)}"
                        @click="filterTag(thisTag.slug)"
                    >
                        {{ thisTag.name }}
                    </a>

                    <span class="block mt-6 mb-2 mx-4 pb-2 px-4 mt-4 border-b border-grey uppercase text-sm">Popular tags</span>

                    <a
                        :key="thisTag.slug"
                        v-for="thisTag in popularTags" class="block px-8 py-2 cursor-pointer hover:text-indigo-dark"
                        :class="{'text-grey-darkest font-bold': thisTag.slug == tag, 'text-grey-darker': thisTag.slug != tag}"
                        @click="filterTag(thisTag.slug)"
                    >
                        {{ thisTag.name }}
                    </a>

                </nav>
            </div>

            <!-- The body -->
            <div class="w-full">
                <!-- Popular and Recent -->
                <div class="w-full" v-show="tag === 'popular---and---recent'">
                    <h2 class="ml-2 mb-2">Recent</h2>
                    <div class="flex flex-wrap justify-center sm:justify-start">
                        <package-card
                            :key="thisPackage.id"
                            v-for="thisPackage in recentPackages"
                            :package="thisPackage"
                        ></package-card>
                    </div>
                    <a href="#" @click.prevent="tag = 'all'" class="font-bold ml-2 mb-6">See More...</a>

                    <h2 class="ml-2 mb-2 mt-8">Popular</h2>
                    <div class="flex flex-wrap justify-center sm:justify-start">
                        <package-card
                            :key="thisPackage.id"
                            v-for="thisPackage in popularPackages"
                            :package="thisPackage"
                        ></package-card>
                    </div>
                </div>

                <!-- All packages, or packages by type -->
                <div class="w-full" v-show="tag !== 'popular---and---recent'">
                    <h2 v-show="tag !== 'popular---and---recent' && tag !== 'all'" class="ml-2 mb-6">Tag: {{ tag }}</h2>
                    <h2 v-show="tag === 'all'" class="ml-2 mb-2">All Packages (newest first)</h2>

                    <div class="" v-show="tag !== 'popular---and---recent'">
                        <div v-if="filtered_packages.length == 0" class="block w-full font-bold text-xl text-grey-dark self-start ml-2">
                            Sorry, but no packages currently in our database match this filter.
                        </div>
                        <div v-cloak v-if="filtered_packages.length" class="flex flex-wrap justify-center sm:justify-start">
                            <package-card
                                :key="thisPackage.id"
                                v-for="thisPackage in filtered_packages"
                                :package="thisPackage"
                            ></package-card>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import _ from 'lodash';
const colors = [
    '#6574cd', // Blue
    '#41ac9c', // Teal
    '#e49334', // Orange
    '#56ad34', // Green
    '#c34949', // Red
    '#a72b9d', // Purple
    '#d2c823', // Yellow
];
let colorIndex = 0;

export default {
    props: {
        auth: Boolean,
        initialPackages: Array,
        initialPopularPackages: Array,
        initialRecentPackages: Array,
        typeTags: Array,
        popularTags: Array,
    },
    data: function() {
        return {
            packages: this.initialPackages,
            recentPackages: this.initialRecentPackages,
            popularPackages: this.initialPopularPackages,
            tag: 'popular---and---recent',
            colorMap: []
        };
    },
    methods: {
        tagBySlugHasNoPackages: function(slug) {
            return this.packages.filter(thisPackage => {
                return thisPackage.tags.map(tag => {
                    return tag.slug;
                }).includes(slug)
            }).length === 0;
        },
        filterTag: function(tag) {
            this.tag = tag;
            window.location.hash = tag;
        },
        startCase: function(string) {
            return _.startCase(string);
        },
        nextColor: function() {
            return colors[colorIndex++ % colors.length];
        },
        decoratePackagesWithColors: function(list) {
            list.forEach((thisPackage) => {
                this.$set(thisPackage, 'accent', this.colorForPackage(thisPackage.id));
            });
        },
        colorForPackage: function(thisPackageId) {
            return this.colorMap[thisPackageId];
        }
    },
    computed: {
        filtered_packages() {
            return this.tag === 'all'
                ? this.packages
                : this.packages.filter((thisPackage) => {
                    return thisPackage.tags.map((tag) => {
                        return tag.slug;
                    }).includes(this.tag)
                });
        }
    },
    mounted: function() {
        let maxPackageid = _.maxBy(this.packages, 'id').id;
        let maxPopularPackageId = _.maxBy(this.popularPackages, 'id').id;
        let maxRecentPackageId = _.maxBy(this.recentPackages, 'id').id;

        let totalMaxId = _.max([maxPackageid, maxPopularPackageId, maxRecentPackageId]);

        let colorMap = {};
        for (let i = 1; i <= totalMaxId; i++) {
            colorMap[i] = this.nextColor();
        }
        this.colorMap = colorMap;
        this.tag = window.location.hash.slice(1) || this.tag;

        this.decoratePackagesWithColors(this.packages);
        this.decoratePackagesWithColors(this.popularPackages);
        this.decoratePackagesWithColors(this.recentPackages);
    }
};
</script>
