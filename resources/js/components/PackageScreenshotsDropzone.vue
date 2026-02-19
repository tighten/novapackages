<template>
    <div>
        <div id="images" class="dropzone"></div>
    </div>
</template>

<script>
import Dropzone from 'dropzone';
Dropzone.autoDiscover = false;

export default {
    mounted() {
        var self = this;

        this.dropzone = new Dropzone("div#images", {
            url: "/app/screenshot-uploads",
            paramName: 'screenshot',
            addRemoveLinks: true,
            method: 'post',
            headers: { 'X-CSRF-TOKEN': novapackages.csrf_token },
            init: function () {
                this.on('success', function (screenshot, response) {
                    self.$emit('screenshotAdded', response);
                    this.removeFile(screenshot);
                });

                this.on('error', function (screenshot, response) {
                    if (response.hasOwnProperty('errors')) {
                        $(screenshot.previewElement).find('.dz-error-message').text(response.errors.screenshot);
                    } else {
                        $(screenshot.previewElement).find('.dz-error-message').text("An error has occurred");
                    }
                });
            }
        });
    }
}
</script>

<style>
    .dropzone .dz-preview .dz-error-message {
        top: 150px;
    }
</style>
