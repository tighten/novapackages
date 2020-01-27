<template>
    <div class="flex flex-col container mx-auto pb-8" style="max-width: 1188px;">
        <p class="block text-center text-2xl mb-10 mx-4 text-grey-darkest">Search results for <code>{{ query }}</code>.</p>

        <package-search :initial-query="query"></package-search>

        <div class="flex flex-col sm:flex-row justify-around">
            <div class="flex flex-no-shink flex-grow">
                <div v-if="packages.length == 0"
                    class="block w-full sm:w-2/3 font-bold text-center md:text-xl text-grey-dark mx-auto my-12 self-start">
                    Sorry, but no packages currently in our database match this search.
                </div>
                <div v-cloak v-if="packages.length" class="flex flex-wrap w-full justify-center">
                    <package-card
                        v-for="thisPackage in packages"
                        :package="thisPackage"
                        :key="thisPackage.id"></package-card>
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
        query: {}
    },
    data: function() {
        return {
            packages: this.initialPackages,
        };
    },
    methods: {
        startCase: function(string) {
            return _.startCase(string);
        },
        nextColor: function() {
            return colors[colorIndex++ % colors.length];
        },
    },
    mounted: function() {
        this.packages = this.packages.map((thisPackage) => {
            this.$set(thisPackage, 'accent', this.nextColor());

            return thisPackage;
        });
    }
};
</script>
