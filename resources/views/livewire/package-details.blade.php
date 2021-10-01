<div>
    @if ($package['possibly_abandoned'])
        <div class="bg-red-600 text-white p-4">
            This package is possibly abandoned. Please proceed with care.
        </div>
    @endif
    @if ($package['marked_as_unavailable_at'])
        <div class="bg-orange-500 text-white p-4">
            This package seems to have a broken documentation URL. Please proceed with care.
        </div>
    @endif
    <div class="bg-gray-700">
        <ul class="flex">
            <li class="ml-4">
                <a
                    class="block bg-gray-800 hover:bg-gray-100 text-gray-100 hover:text-gray-800 text-inherit font-semibold no-underline p-4 sm:mr-2 md:mr-4 md:px-6"
                    href="#readme"
                >
                    Readme
                </a>
            </li>
            @if (count($screenshots))
                <li>
                    <a
                        class="block bg-gray-800 hover:bg-gray-100 text-gray-100 hover:text-gray-800 text-inherit font-semibold no-underline p-4 sm:mr-2 md:mr-4 md:px-6"
                        href="#screenshots"
                    >
                        Screenshots
                    </a>
                </li>
            @endif
            @if (count($package['reviews']))
                <li>
                    <a
                        class="block bg-gray-800 hover:bg-gray-100 text-gray-100 hover:text-gray-800 text-inherit font-semibold no-underline p-4 sm:mr-2 md:mr-4 md:px-6"
                        href="#reviews"
                    >
                        Reviews
                    </a>
                </li>
            @endif
        </ul>
    </div>
    @if (optional(auth()->user())->isAdmin())
        <div class="text-right -mb-8">
            <div
                class="relative"
                x-data="{ opened: false }"
                x-on:click.away="opened = false"
            >
                <div
                    role="button"
                    class="inline-block select-none p-2"
                    x-on:click="opened = ! opened"
                >
                    <span class="appearance-none flex items-center inline-block text-white font-medium">
                        <svg
                            class="h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                        >
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                        </svg>
                    </span>
                </div>
                <div
                    class="absolute right-0 w-auto mr-2"
                    x-show="opened"
                    x-cloak
                >
                    <div class="bg-indigo-600 shadow rounded border overflow-hidden">
                        <a
                           {{-- v-if="package.is_disabled"--}}
                           {{--:href="--}}
                           {{--     route('app.admin.enable-package', package)--}}
                           {{-- "--}}
                           class="no-underline block px-4 py-3 border-b text-white bg-indigo-600 hover:text-white hover:bg-blue"
                        >
                            Enable
                        </a>
                        <a
                            {{--v-else--}}
                           {{--:href="--}}
                           {{--     route('app.admin.disable-package', package)--}}
                           {{-- "--}}
                           class="no-underline block px-4 py-3 border-b text-white bg-indigo-600 hover:text-white hover:bg-blue"
                        >
                            Disable
                        </a>
                    </div>
                </div>
            </div>
            {{--<admin-dropdown>--}}
            {{--        <span--}}
            {{--            slot="link"--}}
            {{--            class="appearance-none flex items-center inline-block text-white font-medium"--}}
            {{--        >--}}
            {{--            <svg--}}
            {{--                class="h-4 w-4"--}}
            {{--                xmlns="http://www.w3.org/2000/svg"--}}
            {{--                viewBox="0 0 20 20"--}}
            {{--            >--}}
            {{--                <path--}}
            {{--                    d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"--}}
            {{--                />--}}
            {{--            </svg>--}}
            {{--        </span>--}}

            {{--    <div--}}
            {{--        slot="dropdown"--}}
            {{--        class="bg-indigo-600 shadow rounded border overflow-hidden"--}}
            {{--    >--}}
            {{--        <a v-if="package.is_disabled"--}}
            {{--           :href="--}}
            {{--                    route('app.admin.enable-package', package)--}}
            {{--                "--}}
            {{--           class="no-underline block px-4 py-3 border-b text-white bg-indigo-600 hover:text-white hover:bg-blue"--}}
            {{--        >--}}
            {{--            Enable--}}
            {{--        </a>--}}

            {{--        <a v-else--}}
            {{--           :href="--}}
            {{--                    route('app.admin.disable-package', package)--}}
            {{--                "--}}
            {{--           class="no-underline block px-4 py-3 border-b text-white bg-indigo-600 hover:text-white hover:bg-blue"--}}
            {{--        >--}}
            {{--            Disable--}}
            {{--        </a>--}}
            {{--    </div>--}}
            {{--</admin-dropdown>--}}
        </div>
    @endif

    {{--<div class="m-4 md:m-10 break-words">--}}
    {{--    <div v-if="package.instructions" class="border-b border-gray-300 pb-6">--}}
    {{--        <h2 class="border-b-2 border-gray-300 bg-gray-200 -mx-4 pl-4 py-2 pt-3 font-bold mb-4 text-2xl text-gray-800">--}}
    {{--            Installation Instructions--}}
    {{--        </h2>--}}

    {{--        <div--}}
    {{--            class="markdown-body bg-white min-h-full mb-4"--}}
    {{--            v-html="package.instructions"--}}
    {{--        ></div>--}}
    {{--    </div>--}}

    {{--    <div class="border-b border-gray-300 pb-6">--}}
    {{--        <h2--}}
    {{--            id="readme"--}}
    {{--            class="border-b-2 border-gray-300 bg-gray-200 -mx-4 pl-4 py-2 pt-3 text-xl md:text-2xl text-gray-800 font-bold mb-4 mt-8"--}}
    {{--        >--}}
    {{--            Readme--}}
    {{--        </h2>--}}

    {{--        <div--}}
    {{--            v-html="packageReadme"--}}
    {{--            class="markdown-body bg-white min-h-full"--}}
    {{--        ></div>--}}
    {{--    </div>--}}

    {{--    <div v-if="screenshots.length" class="border-b border-gray-300 pb-6">--}}
    {{--        <h2--}}
    {{--            id="screenshots"--}}
    {{--            class="border-b-2 border-gray-300 bg-gray-200 -mx-4 pl-4 py-2 pt-3 text-2xl text-gray-800 font-bold mb-4 mt-8"--}}
    {{--        >--}}
    {{--            Screenshots--}}
    {{--        </h2>--}}

    {{--        <package-screenshot-gallery--}}
    {{--            :screenshots="screenshots"--}}
    {{--        />--}}
    {{--    </div>--}}

    {{--    <div v-if="package.reviews.length" class="border-b border-gray-300 pb-6">--}}
    {{--        <h2--}}
    {{--            id="reviews"--}}
    {{--            class="border-b-2 border-gray-300 bg-gray-200 -mx-4 pl-4 py-2 pt-3 text-xl md:text-2xl text-gray-800 font-bold mb-4 mt-8"--}}
    {{--        >--}}
    {{--            Reviews--}}
    {{--        </h2>--}}

    {{--        <review-list--}}
    {{--            :package_id="package.id"--}}
    {{--            :reviewList="package.reviews"--}}
    {{--        />--}}
    {{--    </div>--}}

    {{--    <a--}}
    {{--        href="#top"--}}
    {{--        class="mt-8 block text-center text-indigo-600 hover:text-gray-800 font-semibold no-underline"--}}
    {{--    >--}}
    {{--        Back to Top--}}
    {{--    </a>--}}
    {{--</div>--}}
</div>
