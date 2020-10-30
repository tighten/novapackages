<?php

namespace App\Notifications;

use App\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class NotifyAuthorOfDisabledPackage extends Notification
{
    use Queueable;

    protected $package;

    public function __construct(Package $package)
    {
        $this->package = $package;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $packageRoute = route('packages.show', [$this->package->composer_vendor, $this->package->composer_package]);
        $packageLink = "<a href='_blank' href='{$packageRoute}'>{$this->package->name}</a>";

        return (new MailMessage)
            ->subject('Please double-check your NovaPackages listing for "' . $this->package->name . '"')
            ->line(new HtmlString("You are receiving this email because you have been identified as an author on {$packageLink}."))
            ->line('This is a reminder that NovaPackages recently found an error with the URL that we have listed for that package:')
            ->line(new HtmlString("<a target='_blank' href='{$this->package->url}'>{$this->package->url}</a>"))
            ->line('In an effort to keep NovaPackages up to date, we have disabled your package in our directory. To re-enable the package, please sign in and update the package URL:')
            ->action('Update Package', route('app.packages.edit', $this->package))
            ->line('Thank you for helping us keep NovaPackages up to date!');
    }
}
