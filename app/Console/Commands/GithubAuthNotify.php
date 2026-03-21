<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\GithubAuthNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

#[Signature('githubauth:notify')]
#[Description('Notifies all users that they need to log in with Github.')]
class GithubAuthNotify extends Command
{
    public function handle(): void
    {
        Notification::send(
            User::whereNull('github_username')->get(),
            new GithubAuthNotification
        );
    }
}
