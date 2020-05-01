<?php

namespace App\Providers;

use App\Events\CollaboratorClaimed as CollaboratorClaimedEvent;
use App\Events\NewUserSignedUp;
use App\Events\PackageCreated;
use App\Events\PackageRated;
use App\Listeners\ClaimOrCreateCollaboratorForNewUser;
use App\Listeners\ClearPackageRatingCache;
use App\Listeners\PackageEventSubscriber;
use App\Listeners\SendNewPackageNotification;
use App\Notifications\CollaboratorClaimed;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CollaboratorClaimedEvent::class => [CollaboratorClaimed::class],
        NewUserSignedUp::class => [ClaimOrCreateCollaboratorForNewUser::class],
        PackageCreated::class => [SendNewPackageNotification::class],
        PackageRated::class => [ClearPackageRatingCache::class],
    ];

    protected $subscribe = [
        PackageEventSubscriber::class,
    ];
}
