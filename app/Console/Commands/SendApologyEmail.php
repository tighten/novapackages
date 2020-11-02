<?php

namespace App\Console\Commands;

use App\Notifications\ApologizeForIncorrectGitHubDisconnect;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class SendApologyEmail extends Command
{
    protected $signature = 'temp:send-apology-email';

    protected $description = 'Send apology email to users who incorrectly received notice their package was unavailable on GitHub.';

    public function handle()
    {
        $userIds = json_decode(Storage::disk('local')->get('apologyemailids.json'));
        Notification::send(User::whereIn('id', $userIds)->get(), new ApologizeForIncorrectGitHubDisconnect);
    }
}
