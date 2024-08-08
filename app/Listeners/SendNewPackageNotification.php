<?php

namespace App\Listeners;

use App\Events\PackageCreated;
use App\Notifications\NewPackage;
use App\Tighten;

class SendNewPackageNotification
{
    public function handle(PackageCreated $event): void
    {
        (new Tighten)->notify(new NewPackage($event->package));
    }
}
