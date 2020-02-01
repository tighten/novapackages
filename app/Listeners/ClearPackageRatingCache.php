<?php

namespace App\Listeners;

use App\CacheKeys;
use App\Events\PackageRated;
use App\Package;
use Illuminate\Support\Facades\Cache;

class ClearPackageRatingCache
{
    public function handle(PackageRated $event)
    {
        Cache::forget(CacheKeys::packageAverageRating($event->packageId));

        /* @todo: Can we forget all the usrPackageRatings? Cache tags? */
        // ... at least let's forget the triggering user.
        if ($event->userId) {
            Cache::forget(CacheKeys::userPackageRating($event->userId, $event->packageId));
        }

        Cache::forget(CacheKeys::ratingsCounts(Package::class, $event->packageId));
    }
}
