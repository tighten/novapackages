<template>
    <div class="flex my-1">
        <div class="w-1/3 text-xs pr-1 text-gray-500 text-right">{{ "".padStart(stars, "★") }}</div>
        <div class="w-2/3 w-full bg-gray-200 h-2 mt-1">
            <div class="bg-yellow h-2" :style="`width: ${ percent }%`"></div>
        </div>
    </div>
</template>

<script>
import _ from 'lodash';

export default {
    props: ['count', 'totalCount', 'stars'],
    computed: {
        percent: function () {
            return this.totalCount == 0
                ? 0
                : Math.round(this.count / this.totalCount * 100);
        }
    }
};
</script>
