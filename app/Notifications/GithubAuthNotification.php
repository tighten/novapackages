<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GithubAuthNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Please authenticate your NovaPackages account with GitHub.')
                    ->line('You did it! You snuck in to NovaPackages.com before we were finished working on it 😘.')
                    ->line('We\'ve since added GitHub-based authentication, so you\'ll need to go log back in with GitHub in order to keep your account active.')
                    ->line('If the email you used to create your account is the same as the Email address on your GitHub account, we will merge the two; otherwise you will get a brand new account based on your GitHub account.')
                    ->action('Connect Your GitHub Account', route('home'))
                    ->line('Thank you for being so excited to try NovaPackages!');
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
