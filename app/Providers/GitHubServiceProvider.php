<?php

namespace App\Providers;

use App\Http\Remotes\GitHub;
use Github\Client as GitHubClient;
use Illuminate\Support\ServiceProvider;

class GitHubServiceProvider extends ServiceProvider
{
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
