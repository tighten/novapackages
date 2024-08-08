<?php

namespace App\Listeners;

use App\Notifications\PackageDeleted;
use App\Tighten;

class SendPackageDeletedNotification
{
    public function handle($event): void
    {
        (new Tighten)->notify(new PackageDeleted($event->packageName, $event->user));
    }
}
