<template>
    <div>
        <div v-if="isEditing">
            <form
                class="container py-4"
                id="review"
            >
              <div class="mb-4 flex">
                    <star-rating
                        v-model="user_rating"
                        :rating="user_rating"
                        :read-only="true"
                        :show-rating="false"
                        :star-size="20"
                    />
                </div>

                <textarea
                    v-model="content"
                    class="w-full leading-tight mb-1 p-2 border border-indigo-700"
                    maxLength="5000"
                    minlength="20"
                    ref="review"
                    required
                    rows="5"
                ></textarea>

                <button
                    class="flex justify-center items-center w-full md:w-auto cursor-pointer no-underline text-white bg-indigo-600 hover:bg-indigo-700 py-4 px-4 sm:px-6"
                    type="button"
                    @click="update">
                    Update Review
                </button>
            </form>
        </div>

        <div v-else class="container leading-tight py-4 w-full">
            <template>
                <star-rating
                    v-model="user_rating"
                    :rating="user_rating"
                    :read-only="true"
                    :show-rating="false"
                    :star-size="20"
                />
            </template>

            <p class="text-gray-600 text-sm py-1">By {{author.name}} on {{date}}</p>

            <p class="text-gray-800 py-1">{{ content }}</p>

            <div v-if="canEditReview" class="flex mt-2">
                <button
                    class="cursor-pointer bg-indigo-600 text-white no-underline hover:bg-indigo-700 flex justify-center items-center py-2 px-4 md:px-6"
                    @click="edit"
                >
                    Edit
                </button>

                <button
                    class="flex text-red no-underline font-bold text-md uppercase cursor-pointer ml-2 px-2"
                    @click="remove"
                >
                    Delete
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import _ from 'lodash';
import moment from 'moment';
import StarRating from 'vue-star-rating';

export default {
    props: {
        package_id: {},
        review: {},
    },
    components: {
        StarRating,
    },
    data: function() {
        return {
            author: this.review.user,
            content: this.review.content,
            date: moment(this.review.update_at).format('MMMM D, YYYY'),
            isEditing: false,
            showReview: true,
        };
    },
    computed: {
        canEditReview() {
            return this.userOwnsReview || window.novapackages.is_admin;
        },
        userOwnsReview() {
            return this.author.id == window.novapackages.user_id;
        },
        user_rating() {
            if (!this.review.rating) {
                return 0;
            }
            return this.review.rating.rating
        },
    },
    methods: {
        edit() {
            this.isEditing = true;
        },
        remove() {
            axios.delete(route('internalapi.reviews.delete', {review: this.review.id})).then(
                response => {
                    window.location.reload();
                    this.$nextTick((
                        alert('Review Successfully Deleted')
                    ));
                },
                response => {
                    alert('Error: ' + response.message);
                }
            )
        },
        update() {
            axios.patch(route('internalapi.reviews.update'), {
                package_id: this.package_id,
                review: this.content,
            }).then(
                response => {
                    window.location.reload();
                },
                response => {
                    alert('Error: ' + response.message);
                }
            );
        },
        setRating(rating) {
            axios.post(route('internalapi.ratings.store'), {
                package_id: this.package.id,
                rating: rating,
            }).then(
                response => {
                    this.rating = rating;
                    this.rated = true;
                },
                response => {
                    alert('Error: ' + response.message);
                }
            );
        },
    },
};
</script>
