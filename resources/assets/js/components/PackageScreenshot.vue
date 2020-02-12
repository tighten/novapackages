<template>
    <div class="text-center">
        <img class="rounded shadow-md mb-2" :src="screenshot.public_url" />
        <button @click="deleteScreenshot" class="bg-red hover:bg-red-600 text-white font-bold py-1 px-2 rounded">Delete</button>
    </div>
</template>

<script>
export default {
    props: ['screenshot'],

    methods: {
        deleteScreenshot: function (event) {
            self = this;
            event.preventDefault();
            axios
                .delete('/app/screenshot-uploads/' + this.screenshot.id)
                .then(function (response) {
                    var screenshotIndex = self.screenshots.findIndex(element => element.id == self.screenshot.id);
                    self.screenshots.splice(screenshotIndex, 1);
                })
        }
    },

    computed: {
        screenshots: function () {
            return this.$parent.screenshots;
        }
    }
}
</script>
