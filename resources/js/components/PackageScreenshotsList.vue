<template>
    <div class="flex flex-wrap items-end mt-4 mb-6">
        <package-screenshot
            v-for="screenshot in screenshots"
            :key="screenshot.id"
            :screenshot="screenshot"
            class="w-1/5 p-2"></package-screenshot>
    </div>
</template>

<script>
export default {
    computed: {
        screenshots: function () {
            return this.$parent.screenshots;
        }
    }
}
</script>
