<template>
    <div>
        <review
            v-for="review in sortedReviewList"
            :key="review.id"
            :package_id="package_id"
            :review="review"
        ></review>
    </div>
</template>

<script>
import _ from 'lodash';
import http from '../http';
import Review from './Review.vue';

export default {
    components: {
        Review,
    },

    props: {
        'package_id': {},
        'reviewList': {},
    },

    computed: {
        sortedReviewList() {
            return _.orderBy(this.reviewList, 'updated_at', 'desc');
        },
    },
};
</script>
