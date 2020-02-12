<template>
    <div class="w-full h-full fixed top-0 left-0 text-center" style="background: rgba(0,0,0,0.9);" @click.self="closeLightbox">
        <div class="relative mx-auto m-h-screen max-w-4xl flex items-center justify-center">
            <a class="text-white text-center cursor-pointer pr-4" @click="previous">
                <svg class="fill-current text-white inline-block h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M3.828 9l6.071-6.071-1.414-1.414L0 10l.707.707 7.778 7.778 1.414-1.414L3.828 11H20V9H3.828z"/></svg>
            </a>
            <div class="w-full h-screen">
                <img class="cursor-pointer rounded my-10" style="max-height: 90vh;" :src="this.currentScreenshot.public_url" @click="closeLightbox"/>
            </div>
            <div>
                 <a class="text-white cursor-pointer absolute top-0 pt-4 pl-4" @click="closeLightbox">
                    <svg class="fill-current text-white inline-block h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z"/></svg>
                </a>
                <a class="text-white text-center cursor-pointer pl-4" @click="next">
                    <svg class="fill-current text-white inline-block h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M16.172 9l-6.071-6.071 1.414-1.414L20 10l-.707.707-7.778 7.778-1.414-1.414L16.172 11H0V9z"/></svg>
                </a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['screenshots', 'initialPosition'],

    data() {
        return {
            currentPosition: this.initialPosition,
        }
    },

    computed: {
        currentScreenshot: function () {
            return this.screenshots[this.currentPosition];
        }
    },

     methods: {
        closeLightbox: function () {
            this.$emit('lightboxClosed');
        },
        next: function () {
            this.currentPosition = this.screenshots.length > this.currentPosition + 1 ? this.currentPosition + 1 : 0;
        },
        previous: function () {
            this.currentPosition = this.currentPosition - 1 < 0 ? this.screenshots.length - 1 : this.currentPosition - 1;
        }
    }
}
</script>
