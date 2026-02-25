@props(['screenshots'])

<div
    x-data="{ open: false, current: 0 }"
    @keydown.escape.window="open = false"
    @keydown.arrow-right.window="if (open) current = (current + 1) % {{ count($screenshots) }}"
    @keydown.arrow-left.window="if (open) current = (current - 1 + {{ count($screenshots) }}) % {{ count($screenshots) }}"
    class="flex flex-wrap items-end mt-4 mb-6"
>
    @foreach ($screenshots as $index => $screenshot)
        <div class="text-center w-1/5 p-2">
            <a class="cursor-pointer" @click="open = true; current = {{ $index }}">
                <img class="rounded-sm shadow-md" src="{{ $screenshot->public_url }}" />
            </a>
        </div>
    @endforeach

    {{-- Lightbox --}}
    <template x-teleport="body">
        <div
            x-show="open"
            x-transition.opacity
            class="w-full h-full fixed top-0 left-0 text-center z-50"
            style="background: rgba(0,0,0,0.9);"
            @click.self="open = false"
        >
            <div class="relative mx-auto max-w-4xl flex items-center justify-center h-screen">
                <a class="text-white text-center cursor-pointer pr-4" @click="current = (current - 1 + {{ count($screenshots) }}) % {{ count($screenshots) }}">
                    <svg class="fill-current text-white inline-block h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M3.828 9l6.071-6.071-1.414-1.414L0 10l.707.707 7.778 7.778 1.414-1.414L3.828 11H20V9H3.828z"/></svg>
                </a>
                <div class="w-full">
                    @foreach ($screenshots as $index => $screenshot)
                        <img
                            x-show="current === {{ $index }}"
                            class="cursor-pointer rounded-sm mx-auto"
                            style="max-height: 90vh;"
                            src="{{ $screenshot->public_url }}"
                            @click="open = false"
                        />
                    @endforeach
                </div>
                <div>
                    <a class="text-white cursor-pointer absolute top-0 right-0 pt-4 pr-4" @click="open = false">
                        <svg class="fill-current text-white inline-block h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z"/></svg>
                    </a>
                    <a class="text-white text-center cursor-pointer pl-4" @click="current = (current + 1) % {{ count($screenshots) }}">
                        <svg class="fill-current text-white inline-block h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M16.172 9l-6.071-6.071 1.414-1.414L20 10l-.707.707-7.778 7.778-1.414-1.414L16.172 11H0V9z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </template>
</div>
