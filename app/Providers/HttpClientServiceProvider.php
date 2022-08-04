<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class HttpClientServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Http::macro('github', function () {
            return Http::withToken(config('services.github.token'))
                ->when(app()->environment('production'), fn ($client) => $client->withUserAgent('NovaPackages.com'))
                ->baseUrl('https://api.github.com');
        });

        Http::macro('packagist', function () {
            return Http::acceptJson()
                ->when(app()->environment('production'), fn ($client) => $client->withUserAgent('NovaPackages.com'));
        });
    }
}
