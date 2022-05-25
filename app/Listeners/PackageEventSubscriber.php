<?php

namespace App\Listeners;

use App\Jobs\GeneratePackageOpenGraphImage;

class PackageEventSubscriber
{
    public function handle($e)
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
            \App\Events\PackageCreated::class,
            'App\Listeners\PackageEventSubscriber@handle'
        );

        $events->listen(
            \App\Events\PackageUpdated::class,
            'App\Listeners\PackageEventSubscriber@handle'
        );
    }
}
