<?php

namespace App\Console\Commands;

use App\Notifications\GithubAuthNotification;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class GithubAuthNotify extends Command
{
    protected $signature = 'githubauth:notify';

    protected $description = 'Notifies all users that they need to log in with Github.';

    public function handle()
    {
        Notification::send(
            User::whereNull('github_username')->get(),
            new GithubAuthNotification
        );
    }
}
