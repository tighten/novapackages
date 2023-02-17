<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\Package::class => \App\Policies\PackagePolicy::class,
        \App\Models\Screenshot::class => \App\Policies\ScreenshotPolicy::class,
        \App\Models\Review::class => \App\Policies\ReviewPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
    }
}
