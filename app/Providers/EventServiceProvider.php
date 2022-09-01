<?php

namespace App\Providers;

use App\Events\CollaboratorClaimed as CollaboratorClaimedEvent;
use App\Events\NewUserSignedUp;
use App\Events\PackageCreated;
use App\Events\PackageDeleted;
use App\Events\PackageRated;
use App\Listeners\ClaimOrCreateCollaboratorForNewUser;
use App\Listeners\ClearPackageRatingCache;
use App\Listeners\PackageEventSubscriber;
use App\Listeners\SendNewPackageNotification;
use App\Listeners\SendPackageDeletedNotification;
use App\Notifications\CollaboratorClaimed;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CollaboratorClaimedEvent::class => [CollaboratorClaimed::class],
        NewUserSignedUp::class => [ClaimOrCreateCollaboratorForNewUser::class],
        PackageCreated::class => [SendNewPackageNotification::class],
        PackageRated::class => [ClearPackageRatingCache::class],
        PackageDeleted::class => [SendPackageDeletedNotification::class],
    ];

    protected $subscribe = [
        PackageEventSubscriber::class,
    ];

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
