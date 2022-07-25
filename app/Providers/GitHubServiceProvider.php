<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class GitHubServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Http::macro('github', function () {
            return Http::withToken(config('services.github.token'))
                ->baseUrl('https://api.github.com')
                ->withHeaders(['Accept' => 'application/vnd.github+json']);
        });
    }
}
