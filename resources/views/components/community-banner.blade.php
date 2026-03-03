@php
$current = 'novapackages';

$colors = [
    'laravelversions' => 'text-blue-500',
    'phpreleases' => 'text-teal-500',
    'novapackages' => 'text-indigo-500',
    'laraveltricks' => 'text-amber-500',
    'forgerecipes' => 'text-primary',
];
@endphp
<div class="w-full bg-black text-white text-sm py-2" id="community-banner">
    <div class="relative mx-auto max-w-[1440px] px-4 py-2 flex items-center justify-between">
        <span>Laravel Community Tools by <a href="https://tighten.com" class="hover:underline">Tighten</a></span>

        <button class="sm:hidden border-0 bg-black text-white cursor-pointer p-1 leading-none" aria-label="Toggle navigation" aria-expanded="false">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>

        <nav class="hidden sm:flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 absolute sm:static top-full left-0 right-0 bg-black px-6 sm:px-0 pb-3 sm:pb-0 z-200 sm:z-auto">
            <a href="https://laravelversions.com" class="{{ $current == 'laravelversions' ? $colors[$current] : '' }} hover:underline">Laravel Versions</a>
            <a href="https://phpreleases.com" class="{{ $current == 'phpreleases' ? $colors[$current] : '' }} hover:underline">PHP Releases</a>
            <a href="https://novapackages.com" class="{{ $current == 'novapackages' ? $colors[$current] : '' }} hover:underline">Nova Packages</a>
            <a href="https://laravel-tricks.com" class="{{ $current == 'laraveltricks' ? $colors[$current] : '' }} hover:underline">Laravel Tricks</a>
            <a href="https://forgerecipes.com" class="{{ $current == 'forgerecipes' ? $colors[$current] : '' }} hover:underline">Forge Recipes</a>
        </nav>
    </div>

    <script>
        (function () {
            var banner = document.getElementById('community-banner');
            var toggle = banner.querySelector('button');
            var nav = banner.querySelector('nav');

            toggle.addEventListener('click', function (e) {
                e.stopPropagation();
                nav.classList.toggle('hidden');
                nav.classList.toggle('flex');
                toggle.setAttribute('aria-expanded', nav.classList.contains('flex'));
            });

            document.addEventListener('click', function (e) {
                if (!banner.contains(e.target)) {
                    nav.classList.add('hidden');
                    nav.classList.remove('flex');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        })();
    </script>
</div>
