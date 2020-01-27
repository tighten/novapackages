<template>
    <div class="pb-8">
        <form action="/search" class="block sm:w-2/3 md:w-128 px-2 sm:mx-auto relative" ref="form">
            <input name="q" class="block w-full h-22 py-4 px-6 pr-16 border border-indigo-light" placeholder="Search Packages" v-model="query" style="padding-right: 5rem;" />
            <a href="#" @click.prevent="submit" class="absolute text-sm font-bold text-grey-dark no-underline uppercase pin-t pin-r p-1 mt-4 mr-4 hover:text-grey-darker">Search</a>
        </form>
    </div>
</template>

<script>
export default {
    props: ['initialQuery'],
    data: function() {
        return {
            query: this.initialQuery,
        };
    },
    methods: {
        submit: function() {
            if (! this.query) {
                alert('You are going to have to enter something first, my friend.');
                return false;
            }

            this.$refs.form.submit();
        }
    },
};
</script>
