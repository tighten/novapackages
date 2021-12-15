<template>
    <div>
        <div class="p-4 text-white bg-red-600" v-if="package.possibly_abandoned">
            This package is possibly abandoned. Please proceed with care.
        </div>
        <div class="p-4 text-white bg-orange-500" v-if="package.marked_as_unavailable_at">
            This package seems to have a broken documentation URL. Please proceed with care.
        </div>
        <div class="bg-gray-700">
            <ul class="flex">
                <li class="ml-4">
                    <a
                        class="block p-4 font-semibold text-gray-100 no-underline bg-gray-800 hover:bg-gray-100 hover:text-gray-800 sm:mr-2 md:mr-4 md:px-6"
                        href="#readme"
                        >Readme</a
                    >
                </li>

                <li v-if="screenshots.length" class="">
                    <a
                        class="block p-4 font-semibold text-gray-100 no-underline bg-gray-800 hover:bg-gray-100 hover:text-gray-800 sm:mr-2 md:mr-4 md:px-6"
                        href="#screenshots"
                        >Screenshots</a
                    >
                </li>

                <li v-if="package.reviews.length" class="">
                    <a
                        class="block p-4 font-semibold text-gray-100 no-underline bg-gray-800 hover:bg-gray-100 hover:text-gray-800 sm:mr-2 md:mr-4 md:px-6"
                        href="#reviews"
                        >Reviews</a
                    >
                </li>
            </ul>
        </div>

        <div v-if="novapackages.is_admin" class="-mb-8 text-right">
            <admin-dropdown>
                <span
                    slot="link"
                    class="flex items-center inline-block font-medium text-white appearance-none"
                >
                    <svg
                        class="w-4 h-4"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                    >
                        <path
                            d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
                        />
                    </svg>
                </span>

                <div
                    slot="dropdown"
                    class="overflow-hidden bg-indigo-600 border rounded shadow"
                >
                    <a v-if="package.is_disabled"
                        :href="
                            route('app.admin.enable-package', package)
                        "
                        class="block px-4 py-3 text-white no-underline bg-indigo-600 border-b hover:text-white hover:bg-blue"
                    >
                        Enable
                    </a>

                    <a v-else
                        :href="
                            route('app.admin.disable-package', package)
                        "
                        class="block px-4 py-3 text-white no-underline bg-indigo-600 border-b hover:text-white hover:bg-blue"
                    >
                        Disable
                    </a>
                </div>
            </admin-dropdown>
        </div>

        <div class="m-4 break-words md:m-10">
            <div v-if="package.instructions" class="pb-6 border-b border-gray-300">
                <h2 class="py-2 pt-3 pl-4 mb-4 -mx-4 text-2xl font-bold text-gray-800 bg-gray-200 border-b-2 border-gray-300">
                    Installation Instructions
                </h2>

                <div
                    class="min-h-full mb-4 bg-white markdown-body"
                    v-html="package.instructions"
                ></div>
            </div>

            <div class="pb-6 border-b border-gray-300">
                <h2
                    id="readme"
                    class="py-2 pt-3 pl-4 mt-8 mb-4 -mx-4 text-xl font-bold text-gray-800 bg-gray-200 border-b-2 border-gray-300 md:text-2xl"
                >
                    Readme
                </h2>

                <div
                    v-html="packageReadme"
                    class="min-h-full bg-white markdown-body"
                ></div>
            </div>

            <div v-if="screenshots.length" class="pb-6 border-b border-gray-300">
                <h2
                    id="screenshots"
                    class="py-2 pt-3 pl-4 mt-8 mb-4 -mx-4 text-2xl font-bold text-gray-800 bg-gray-200 border-b-2 border-gray-300"
                >
                    Screenshots
                </h2>

                <package-screenshot-gallery
                    :screenshots="screenshots"
                />
            </div>

            <div v-if="package.reviews.length" class="pb-6 border-b border-gray-300">
                <h2
                    id="reviews"
                    class="py-2 pt-3 pl-4 mt-8 mb-4 -mx-4 text-xl font-bold text-gray-800 bg-gray-200 border-b-2 border-gray-300 md:text-2xl"
                >
                    Reviews
                </h2>

                <review-list
                    :package_id="package.id"
                    :reviewList="package.reviews"
                />
            </div>

            <a
                href="#top"
                class="block mt-8 font-semibold text-center text-indigo-600 no-underline hover:text-gray-800"
            >
                Back to Top
            </a>
        </div>
    </div>
</template>

<script>
import PackageScreenshotGallery from './PackageScreenshotGallery.vue';
import ReviewList from './ReviewList.vue';

export default {
    components: {
        PackageScreenshotGallery,
        ReviewList,
    },

    props: {
        package: {},
        screenshots: {},
    },

    computed: {
        packageReadme: function() {
            if (this.package.readme) {
                return this.package.readme;
            }

            return `<p>Readme not found. Refer to the project website: <a href="${
                this.package.url
            }">${this.package.url}</a></p>`;
        },
    },
};
</script>
