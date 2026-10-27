<?php

namespace App\Listeners;

use App\Events\PackageCreated;
use App\Events\PackageUpdated;
use App\Jobs\GeneratePackageOpenGraphImage;

class PackageEventSubscriber
{
    public function handle($e): void
    {
        GeneratePackageOpenGraphImage::dispatch(
            $e->package->name,
            $e->package->author->name,
            $e->package->og_image_name
        );
    }

    public function subscribe($events)
    {
        $events->listen(
            PackageCreated::class,
            'App\Listeners\PackageEventSubscriber@handle'
        );

        $events->listen(
            PackageUpdated::class,
            'App\Listeners\PackageEventSubscriber@handle'
        );
    }
}
