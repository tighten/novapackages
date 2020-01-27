<template>
    <div class="flex flex-wrap items-end mt-4 mb-6">
        <div v-for="(screenshot, index) in screenshots" class="text-center w-1/5 p-2">
            <a class="cursor-pointer" @click="showLightbox(index)">
                <img class="rounded shadow-md" :src="screenshot.public_url" />
            </a>
        </div>

        <transition name="fade">
            <package-screenshot-gallery-lightbox
                v-if="lightboxActive"
                :screenshots="screenshots"
                :initialPosition="currentPosition"
                @lightboxClosed="closeLightbox"
            />
        </transition>
    </div>
</template>

<script>
import PackageScreenshotGalleryLightbox from './PackageScreenshotGalleryLightbox.vue';

export default {
    components: {
        PackageScreenshotGalleryLightbox,
    },

    props: {
        screenshots: {},
    },

    data() {
        return {
            lightboxActive: false,
            currentPosition: 0,
        }
    },

    methods: {
        showLightbox: function (index) {
            this.lightboxActive = true;
            this.currentPosition = index;
        },
        closeLightbox: function () {
            this.lightboxActive = false;
        },
    },
}
</script>
