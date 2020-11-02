<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApologizeForIncorrectGitHubDisconnect extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Our apologies for the incorrect error email!")
            ->line("Yesterday you received an email from NovaPackages saying your package's URL can't be reached. However, this was due to an overeager notification script that didn't consider GitHub's rate limiting.")
            ->line("We've disabled that script until we can help it grow up a little. Sorry for the inconvenience!")
            ->line("If you'd like, you can log into Nova Packages now to view or edit any of your packages:")
            ->action('View My Packages', route('app.packages.index'))
            ->line('Thank you for listing your package(s) on NovaPackages!');
    }
}
