<?php

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Facade;

return [

    'timezone' => 'UTC',


    'aliases' => Facade::defaultAliases()->merge([
        'Bugsnag' => Bugsnag\BugsnagLaravel\Facades\Bugsnag::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
    ])->toArray(),

];
