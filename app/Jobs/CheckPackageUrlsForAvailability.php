<?php

namespace App\Jobs;

use App\Notifications\NotifyContributorOfUnavailablePackageUrl;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Zttp\Zttp;

class CheckPackageUrlsForAvailability implements ShouldQueue
{

    private $package;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct($package)
    {
        $this->package = $package;
    }

    public function handle()
    {
        $urlIsValid = true;
        try {
            if (Zttp::get($this->package->url)->isClientError()) $urlIsValid = false;
        } catch (Exception $e) {
            $urlIsValid = false; // If we can't reach the domain at all, mark as invalid
        }

        if ($urlIsValid) return;

        $this->package->marked_as_unavailable_at = now();
        $this->package->save();

        if ($this->package->author && $this->package->authorIsUser()) {
            $this->package->author->user->notify(new NotifyContributorOfUnavailablePackageUrl($this->package));
        }
    }
}
