<template>
    <div class="m-4 md:m-10">
        <h1 class="text-lg text-indigo-700 font-normal pt-2 pb-6">
            To submit a review, select a star rating and enter your comments below.
        </h1>

        <div class="container mb-4 flex">
            <div class="pt-1 text-gray-600">Your Rating:</div>

            <div class="w-2/3 pl-2 flex-row">
                <star-rating
                    :rating="rating"
                    :show-rating="false"
                    :star-size="20"
                    @rating-selected="setRating"
                />
            </div>
        </div>

        <textarea
            v-model="review"
            class="w-full mb-1 p-2 border border-indigo-700"
            autofocus
            maxLength="5000"
            minlength="20"
            placeholder="Write Your Review Here"
            ref="review"
            required
            rows="5"
        ></textarea>

        <button v-if="canSubmit"
            class="cursor-pointer bg-indigo-600 hover:bg-indigo-700 w-full md:w-auto text-white rounded-sm no-underline flex justify-center items-center mt-2 py-4 px-4 sm:px-6"
            @click.prevent="save"
        >
            Submit Review
        </button>
    </div>
</template>

<script>
import StarRating from 'vue-star-rating';

export default {
    props: {
        package: {},
        starRating: { default: null }
    },

    components: {
        StarRating
    },

    data: function() {
        return {
            review: null,
            rating: this.starRating ? this.starRating.rating : 0,
            rated: !!this.starRating
        };
    },

    computed: {
        canSubmit: function() {
            return !!this.rated && !!this.review;
        }
    },

    methods: {
        setRating(rating) {
            axios
                .post('/internalapi/ratings', {
                    package_id: this.package.id,
                    rating: rating
                })
                .then(
                    response => {
                        console.log(response);
                        this.rating = rating;
                        this.rated = true;
                    },
                    response => {
                        alert('Error: ' + response.message);
                    }
                );
        },
        save() {
            let composerData = this.package.composer_name.split('/');

            axios
                .post(route('internalapi.reviews.store'), {
                    package_id: this.package.id,
                    review: this.review
                })
                .then(
                    response => {
                        window.location.replace(
                            route('packages.show', [
                                composerData[0],
                                composerData[1]
                            ])
                        );
                    },
                    response => {
                        alert('Error: ' + response.message);
                    }
                );
        }
    }
};
</script>
