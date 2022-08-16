<template>
    <div class="container mx-auto flex flex-col items-center mb-8 px-2">

        <div class="w-full flex flex-col md:flex-row">
            <div
                class="w-full md:w-3/4 bg-white shadow"
                :class="[
                    package.is_disabled ? 'border-red' : 'border-indigo'
                ]"
            >
                <slot />
            </div>

            <div
                class="w-full md:w-1/4 bg-white md:ml-4 md:mt-12 shadow text-sm border-t sm:border-t-0"
            >
                <a
                    :href="'/app/packages/' + package.id + '/edit'"
                    class="block bg-indigo-600 hover:bg-indigo-700 text-white no-underline font-bold p-4 md:p-8 py-4 border-gray-300 border-b"
                    v-if="package.current_user_owns"
                >
                    Edit this package
                </a>

                <div class="px-4 md:px-6 py-4 border-gray-300 border-b">
                    <table class="w-full">
                        <tr>
                            <td class="font-bold py-2">Added</td>

                            <td class="py-2">{{ package.created_at }}</td>
                        </tr>

                        <template v-if="package.composer_latest">
                            <tr>
                                <td class="font-bold py-2">Last updated</td>

                                <td class="py-2">
                                    {{
                                        humanTime(package.composer_latest.time)
                                    }}
                                </td>
                            </tr>

                            <tr>
                                <td class="font-bold py-2">Version</td>

                                <td class="py-2">
                                    {{ package.composer_latest.version }}
                                </td>
                            </tr>

                            <tr v-if="package.nova_version">
                                <td class="py-2 font-bold">Nova Version</td>

                                <td class="py-2">
                                    {{ package.nova_version }}
                                </td>
                            </tr>

                            <tr>
                                <td class="font-bold pt-2" colspan="2>">Composer</td>
                            </tr>

                            <tr>
                                <td class="pb-2" colspan="2">
                                    <a
                                        :href="
                                            'https://packagist.org/packages/' +
                                                package.composer_name
                                        "

                                        class="text-indigo-600 underline"
                                        >{{ package.composer_name }}</a
                                    >
                                </td>
                            </tr>

                            <tr>
                                <td class="font-bold py-2">GitHub stars</td>

                                <td class="py-2">
                                    {{ package.github_stars }}
                                </td>
                            </tr>

                            <tr>
                                <td class="font-bold py-2">Packagist downloads</td>

                                <td class="py-2">
                                    {{ packagistDownloads }}
                                </td>
                            </tr>
                        </template>
                    </table>

                    <div v-if="!package.composer_latest">
                        <p class="mb-2" v-if="package.composer_data">
                            This package is listed on
                            <a
                                :href="
                                    'https://packagist.org/packages/' +
                                        package.composer_name +
                                        '.json'
                                "
                                >the Packagist API</a
                            >, but has no stable tags.
                        </p>

                        <p class="mb-2" v-else>
                            This package should be on
                            <a
                                :href="
                                    'https://packagist.org/packages/' +
                                        package.composer_name +
                                        '.json'
                                "
                                >the Packagist API</a
                            >
                            but we're not getting any results.
                        </p>

                        <p class="mb-2">
                            Please note that the Packagist cache is pretty long,
                            so some times you just need to check back in an
                            hour.
                        </p>

                        <a
                            href="#"
                            @click.prevent="requestPackagistRefresh"
                            v-if="
                                package.current_user_owns && !refreshRequested
                            "
                            class="block mt-8 mb-2"
                        >
                            Request a cache refresh from Packagist (the cache
                            lasts 5 minutes)
                        </a>

                        <span v-if="refreshRequested" class="block mt-8 mb-2">
                            Refresh requested
                        </span>
                    </div>

                    <div>
                        <a
                            href="#"
                            @click.prevent="requestRepositoryRefresh"
                            v-if="
                                package.current_user_owns &&
                                    !repositoryRefreshRequested
                            "
                            class="block mt-8 mb-2"
                        >
                            Request a refresh of the readme from your package
                            registry or VCS provider.
                        </a>

                        <span
                            v-if="repositoryRefreshRequested"
                            class="block mt-8 mb-2"
                        >
                            Refresh requested
                        </span>
                    </div>
                </div>

                <div
                    class="p-4 md:p-6 border-solid border-gray-300 border-b overflow-hidden"
                    style="text-overflow: ellipsis;white-space: nowrap;"
                    v-if="package.url"
                >
                    <h3 class="uppercase text-gray-600 text-sm pb-2 font-bold">URL</h3>

                    <a :href="package.url" class="text-indigo-600 underline">{{ package.url }}</a>
                </div>

                <div class="p-4 pb-0 md:p-6 md:pb-2 border-gray-300 border-b">
                    <h3 class="uppercase text-gray-600 text-sm font-bold">Favorites</h3>

                    <div class="block py-4">
                        {{ favoritesCountString }} favorited
                    </div>

                    <a
                        v-if="auth"
                        @click="toggleFavorite"
                        class="block text-indigo-600 no-underline font-bold text-sm cursor-pointer pb-4"
                    >
                        {{ favoritePackageLinkText }}
                    </a>
                </div>

                <div v-if="!creatingReview" class="p-4 md:p-6 pb-4 border-gray-300 border-b">
                    <h3 class="uppercase text-gray-600 text-sm font-bold">Rating</h3>

                    <div class="flex" v-if="!rated">
                        <div class="mt-2 mb-4 text-5xl w-1/2">
                            {{
                                package.average_rating
                                    ? package.average_rating
                                    : 'None yet'
                            }}
                        </div>

                        <div class="w-1/2 mb-6 text-gray-500 self-end">
                            (out of 5)
                        </div>
                    </div>

                    <div class="mt-2 mb-4" v-else>
                        Thanks for rating this package!
                    </div>

                    <div v-if="auth && !isSelfAuthored && !isSelfContributed" class="mb-4 flex">
                        <div class="w-1/3 pt-1 text-gray-600">
                            Tap to rate:
                        </div>

                        <div class="w-2/3 pl-2">
                            <star-rating
                                v-model="package.current_user_rating"
                                :rating="package.current_user_rating"
                                :read-only="!auth"
                                :star-size="20"
                                :show-rating="false"
                                @rating-selected="setRating"
                            ></star-rating>
                        </div>
                    </div>

                     <rating-count-bar
                        :totalCount="totalRatings"
                        :stars="rating_count.number"
                        :count="rating_count.count"
                        :key="package.id + 'rate' + rating_count.number"
                        v-for="rating_count in package.rating_counts"
                    />

                    <div class="text-right text-sm text-gray-600 mt-2 mb-2">
                        {{ totalRatings }} ratings
                    </div>

                    <div v-if="auth && !package.current_user_review.length && !isSelfAuthored && !isSelfContributed">
                        <a
                            class="block text-indigo-600 no-underline font-bold text-sm cursor-pointer pb-4"
                            :href="route('reviews.create', {
                                namespace: package.packagist_namespace,
                                name: package.packagist_name,
                            })"
                        >
                            Review This Package
                        </a>
                    </div>
                </div>

                <div class="p-4 md:p-6 border-gray-300 border-b">
                    <h3 class="uppercase text-gray-600 text-sm font-bold">Author</h3>

                    <div class="flex text-sm pt-4 items-center">
                        <img
                            :src="package.author.avatar_url"
                            class="rounded-full h-10 w-10 mr-4"
                            :alt="package.author.name"
                        />

                        <a
                            :href="
                                '/collaborators/' +
                                    package.author.github_username
                            "
                            class="text-indigo-600 font-bold no-underline uppercase text-sm hover:text-indigo-700"
                        >
                            {{ package.author.name }}
                        </a>
                    </div>
                </div>

                <div
                    class="p-4 pb-2 md:p-6 border-gray-300 border-b"
                    v-if="package.contributors.length"
                >
                    <h3 class="uppercase text-gray-600 text-sm font-bold">
                        Contributors
                    </h3>

                    <div
                        v-for="contributor in package.contributors"
                        class="flex text-sm pt-4 items-center"
                    >
                        <title-icon
                            :title="contributor.name"
                            size="medium"
                            :image="contributor.avatar_url"
                        ></title-icon>

                        <a
                            class="text-indigo-600 font-bold no-underline uppercase text-sm hover:text-indigo-700"
                        >
                            {{ contributor.name }}
                        </a>
                    </div>
                </div>

                <div class="p-4 pb-0 md:p-6 md:pb-2 border-gray-300 border-b">
                    <h3 class="uppercase text-gray-600 text-sm font-bold">Tags</h3>

                    <div class="block py-4">
                        <button
                            class="bg-indigo-200 text-indigo-600 rounded-l-full rounded-r-full px-4 py-2 mr-2 mb-2 inline-block font-bold"
                            @click="viewTag(tag)"
                            v-for="tag in package.tags"
                            :key="tag.slug"
                        >
                            {{ tag.name }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
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

        goBack() {
            window.history.back();
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
