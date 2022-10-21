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
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        CollaboratorClaimedEvent::class => [CollaboratorClaimed::class],
        NewUserSignedUp::class => [ClaimOrCreateCollaboratorForNewUser::class],
        PackageCreated::class => [SendNewPackageNotification::class],
        PackageRated::class => [ClearPackageRatingCache::class],
        PackageDeleted::class => [SendPackageDeletedNotification::class],
        Registered::class => [SendEmailVerificationNotification::class],
    ];

    protected $subscribe = [
        PackageEventSubscriber::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

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
