<template>
    <div :class="this.cssClass">
        <img v-if="image" :src="image" :class="[this.cssClass, this.imageAdditionalClasses]"/>

        <div v-else :class="[this.cssClass, this.noImageAdditionalClasses]"
            :style="'background-color: ' + this.color">
            {{ title.replace('Nova ', '').substring(0, 1).toUpperCase() }}
        </div>
    </div>
</template>

<script>
export default {
    props: {
        color: { default: '#606f7b' },
        image: {},
        size: { default: 'small' },
        title: {}
    },
    data: function() {
        return {
            iconSizes: {
                'small': 'h-8 w-8 mr-2 text-md',
                'medium': 'h-10 w-10 mr-4 text-md',
                'large': 'h-16 w-16 mr-4 text-lg',
                'xlarge': 'h-24 w-24 mr-6 text-xl',
            },
            imageAdditionalClasses: 'rounded-full inline-block bg-gray-600',
            noImageAdditionalClasses:
                'rounded-full inline-block text-white text-center leading-none flex flex-row items-center justify-center'
        };
    },
    computed: {
        cssClass: function() {
            return this.iconSizes[this.size];
        }
    }
};
</script>
