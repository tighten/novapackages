<div class="m-1 p-6 my-3 border-grey border rounded shadow ">
    <div class="flex mb-2">
        <div class="flex-1">
            <a class="text-indigo-600 text-xl no-underline font-bold hover:underline" href="{{ route('packages.show', [$package->composer_vendor, $package->composer_package]) }}">{{ $package->name }}</a>
        </div>
        <div class="uppercase text-gray-600 text-xs">
            <div class="flex px-4 pb-1 items-center">
                <span>Rating: </span>
                <div class="text-md px-2 capitalize">{{ $package->average_rating ?? 'Not Yet Rated' }}</div>
                @if ($package->average_rating )
                    <div class="text-grey text-xs lowercase">(out of 5)</div>
                @endif
            </div>
            <span class="px-4">Downloads: {{ $package->packagist_downloads }}</span>
            <span class="px-4">Stars: {{ $package->github_stars }}</span>
        </div>
    </div>
    <p>{{ $package->abstract }}</p>
</div>
