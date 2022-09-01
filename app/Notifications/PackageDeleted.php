<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class PackageDeleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $packageName, public User $actor)
    {
        //
    }

    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->warning()
            ->content("Package {$this->packageName} was deleted by {$this->actor->name} (user id: {$this->actor->id})");
    }
}
