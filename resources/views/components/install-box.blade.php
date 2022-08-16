@props(['package'])

<div
    x-data="{ opened: true }"
    class="relative"
>
    <a
        x-on:click="opened = true"
        class="block cursor-pointer md:inline-block w-full md:w-auto py-4 px-4 sm:px-6 bg-indigo-600 text-white md:rounded-l-full md:rounded-r-full no-underline hover:bg-indigo-700 flex flex-row justify-center items-center content-center"
    >
        <svg
            class="mr-4 inline-block fill-current w-4"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
        >
            <path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z" />
        </svg>

        <span class="mr-4 inline-block leading-none inline-block uppercase">Install</span>

        <svg
            x-show="opened"
            x-cloak
            class="inline-block fill-current w-4 h-4"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
        >
            <path d="M10.707 7.05L10 6.343 4.343 12l1.414 1.414L10 9.172l4.243 4.242L15.657 12z" />
        </svg>

        <svg
            x-show="! opened"
            class="inline-block fill-current w-4"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
        >
            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
        </svg>
    </a>

    <div
        class="absolute shadow rounded bg-white right-0"
        x-show="opened"
        x-on:click.away="opened = false"
        x-cloak
        style="top: calc(100% + 1rem); min-width: 380px;"
    >
        <div class="flex flex-row w-full p-6 px-8 items-center text-gray-500">
            <input
                id="packagist-install"
                type="text"
                class="rounded flex-grow block border py-2 px-2 mr-4 font-mono text-xs text-black outline-none"
                {{--:class="copyWasSuccessful ? 'border-green border-2' : ''"--}}
                value="composer require {{ $package['composer_name'] }}"
            />

            <svg
                {{--v-if="copyWasSuccessful"--}}
                class="fill-current w-6 h-6 hover:text-indigo-600 pointer-cursor"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
            >
                <path d="M0 11l2-2 5 5L18 3l2 2L7 18z" />
            </svg>

            <svg
                {{--v-else--}}
                id="copy-button"
                class="fill-current w-6 h-6 hover:text-indigo-600 pointer-cursor"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                {{--@click="copyTextToClipboard"--}}
            >
                <path d="M6 6V2c0-1.1.9-2 2-2h10a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-4v4a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V8c0-1.1.9-2 2-2h4zm2 0h4a2 2 0 0 1 2 2v4h4V2H8v4zM2 8v10h10V8H2z" />
            </svg>
        </div>

        @isset ($package['composer_data']['package'])
            <div
                class="border border-t border-gray-300er flex flex-row flex-no-wrap"
            >
                <a
                    href="{{ data_get($package, 'composer_data.package.repository') }}"
                    class="no-underline text-center text-indigo-600 uppercase text-sm font-bold w-1/2 py-4 border-r border-gray-300 hover:bg-indigo-100"
                >
                    GitHub
                </a>

                @isset ($package['composer_latest']['dist']['url'])
                    <a
                        href="{{ $package['composer_latest']['dist']['url'] }}"
                        class="no-underline text-center text-indigo-600 uppercase text-sm font-bold w-1/2 py-4 hover:bg-indigo-100"
                    >
                        Download Zip
                    </a>
                @endisset
            </div>
        @endisset
    </div>
</div>
