@props(['class' => ''])

<div class="relative {{ $class }}" x-data="{ open: false }" @click.away="open = false">
    <div role="button" class="inline-block select-none p-2" @click="open = !open">
        {{ $trigger ?? '' }}
    </div>
    <div class="absolute right-0 w-auto mr-2" x-show="open" x-cloak>
        {{ $slot }}
    </div>
</div>
