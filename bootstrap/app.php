<?php

use App\Http\Middleware\CheckForEmailAddress;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckUserClaimedCollaborator;
use App\Providers\AppServiceProvider;
use App\Providers\HorizonServiceProvider;
use App\Providers\HttpClientServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        AppServiceProvider::class,
        HorizonServiceProvider::class,
        HttpClientServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        // channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectTo(
            guests: fn () => route('login'),
            users: AppServiceProvider::HOME,
        );

        $middleware->throttleApi();

        $middleware->alias([
            'claimed' => CheckUserClaimedCollaborator::class,
            'email' => CheckForEmailAddress::class,
            'role' => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (Throwable $e) {
            if (request()->is('livewire/*') && str_contains((string) request()->userAgent(), 'python-requests')) {
                return false;
            }
        });
    })->create();
