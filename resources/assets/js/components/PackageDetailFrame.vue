<template>
    <div>
        <slot />
    </div>
</template>

<script>
import _ from 'lodash';
import moment from 'moment';
import StarRating from 'vue-star-rating';
import http from '../http';

export default {
    components: {
        StarRating
    },

    props: {
        auth: {},
        authId: {},
        creatingReview: { default: false },
        initialPackage: {},
        ratings: {},
    },

    data: function() {
        return {
            favoritesCount: this.initialPackage.favorites_count,
            isFavorite: this.initialPackage.is_favorite,
            leaveReview: false,
            package: this.initialPackage,
            rated: false,
            refreshRequested: false,
            repositoryRefreshRequested: false,
            reviewed: false,
            reviewText: null,
            wantsToReview: false
        };
    },

    computed: {
        totalRatings: function() {
            return this.package.rating_counts.reduce((carry, rating_count) => {
                return carry + rating_count.count;
            }, 0);
        },

        favoritePackageLinkText: function() {
            return this.isFavorite ? 'Remove Favorite' : 'Add to Favorites';
        },

        favoritesCountString: function() {
            const baseString = `${this.favoritesCount} user`;

            return this.favoritesCount == 1 ? baseString : `${baseString}s`;
        },

        packagistDownloads: function() {
            return this.package.packagist_downloads.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },

        isSelfAuthored: function() {
            return this.auth && this.package.author.id === this.authId;
        },

        isSelfContributed: function() {
            return this.package.contributors.filter((contributor) => {
                return contributor.id === this.authId;
            }).length;
        },
    },

    methods: {
        startCase: function(string) {
            return _.startCase(string);
        },

        humanTime: function(string) {
            if (string) {
                return _.startCase(moment(string).fromNow());
            }

            return 'Nothing yet.';
        },

        setRating(rating) {
            http.post('/internalapi/ratings', {
                package_id: this.package.id,
                rating: rating,
            }).then(response => {
                this.rating = rating;
                this.rated = true;
            }).catch((error) => {
                alert('Error: ' + error.response.data.message);
            });
        },

        requestPackagistRefresh() {
            http.delete(
                '/app/packages/' + this.package.id + '/packagist-cache'
            ).then(
                response => {
                    this.refreshRequested = true;
                },

                response => {
                    alert('Error: ' + response.message);
                }
            );
        },

        requestRepositoryRefresh() {
            http.post(
                '/app/packages/' + this.package.id + '/repository-refresh'
            ).then(
                response => {
                    this.repositoryRefreshRequested = true;
                },

                response => {
                    alert('Error: ' + response.message);
                }
            );
        },

        viewTag(tag) {
            window.location = tag.url;
        },

        toggleFavorite() {
            if (this.isFavorite) {
                this.removeFromFavorites();
            } else {
                this.addToFavorites();
            }
        },

        addToFavorites() {
            http.post(
                `/internalapi/packages/${this.package.id}/favorites`
            ).then(
                response => {
                    this.isFavorite = true;
                    this.favoritesCount++;
                },

                response => {
                    alert('Error: ' + response.message);
                }
            );
        },

        removeFromFavorites() {
            http.delete(
                `/internalapi/packages/${this.package.id}/favorites`
            ).then(
                response => {
                    this.isFavorite = false;
                    this.favoritesCount--;
                },

                response => {
                    alert('Error: ' + response.message);
                }
            );
        },

        leaveNewReview() {
            this.leaveReview = true;
        },

        showReviewTextBox(event) {
            this.wantsToReview = true;
            this.reviewText = event instanceof MouseEvent ? null : event;
        },

        submitReview(event) {
            http.post('/internalapi/reviews', {
                package_id: this.package.id,
                review: this.reviewText
            }).then(
                response => {
                    window.location.reload();
                },
                response => {
                    alert('Error: ' + response.message);
                }
            );
        },
    }
};
</script>
