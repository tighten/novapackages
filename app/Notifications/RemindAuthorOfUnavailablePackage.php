<?php

namespace App\Notifications;

use App\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class RemindAuthorOfUnavailablePackage extends Notification
{
    use Queueable;

    protected $package;

    public function __construct(Package $package)
    {
        $this->package = $package;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $packageRoute = route('packages.show', [$this->package->composer_vendor, $this->package->composer_package]);
        $packageLink = "<a href='_blank' href='{$packageRoute}'>{$this->package->name}</a>";

        return (new MailMessage)
            ->subject('Please double-check your NovaPackages listing for "' . $this->package->name . '"')
            ->line(new HtmlString("You are receiving this email because you have been identified as an author on {$packageLink}."))
            ->line('This is a reminder that NovaPackages recently found an error with the URL that we have listed for that package:')
            ->line(new HtmlString("<a target='_blank' href='{$this->package->url}'>{$this->package->url}</a>"))
            ->line('Could you please verify that URL is still correct? Or, if your package is no longer being maintained, could you please remove it from the directory?')
            ->action('Update Package', route('app.packages.edit', $this->package))
            ->line('If we cannot verify the package URL within two weeks, we will disable the package. Thank you for helping us keep NovaPackages up to date!');
    }
}
