<?php

namespace App\Listeners;

use App\Events\PackageDeleted as PackageDeletedEvent;
use App\Notifications\PackageDeleted;
use App\Tighten;

class SendPackageDeletedNotification
{
    public function handle(PackageDeletedEvent $event): void
    {
        (new Tighten)->notify(new PackageDeleted($event->packageName, $event->user));
    }
}
