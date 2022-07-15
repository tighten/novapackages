<?php

namespace App\Providers;

use App\Http\Remotes\GitHub;
use Github\Client as GitHubClient;
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

    public function register()
    {
        $this->app->bind(GitHubClient::class, function ($app) {
            $client = new GitHubClient(null, 'squirrel-girl-preview');

            if ($app->environment() !== 'testing') {
                $client->authenticate(config('services.github.token'), GitHubClient::AUTH_ACCESS_TOKEN);
            }

            return $client;
        });

        $this->app->singleton(GitHub::class, function () {
            return new GitHub(app(GitHubClient::class));
        });
    }
}
