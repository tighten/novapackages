<template>
    <div>
        <package-screenshots-dropzone @screenshotAdded="addScreenshot"></package-screenshots-dropzone>
        <package-screenshots-list></package-screenshots-list>
        <input v-for="screenshot in screenshots" type="hidden" name="screenshots[]" :value="screenshot.id" />
    </div>
</template>

<script>
export default {
    props: [
        'initialScreenshots'
    ],

    data() {
        return {
            screenshots: this.initialScreenshots,
        }
    },

    methods: {
        addScreenshot: function (screenshot) {
            this.screenshots.push(screenshot);
        }
    }
}
</script>
