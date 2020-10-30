<?php

namespace App\Jobs;

use App\Notifications\NotifyAuthorOfUnavailablePackageUrl;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

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
            $response = Http::get($this->package->url);
            if ($response->clientError()) {
                 $urlIsValid = false;
            }
        } catch (Exception $e) {
            $urlIsValid = false; // If we can't reach the domain at all, mark as invalid
        }

        if ($urlIsValid) return;

        $this->package->marked_as_unavailable_at = now();
        $this->package->save();

        if ($this->package->author && $this->package->authorIsUser()) {
            $this->package->author->user->notify(new NotifyAuthorOfUnavailablePackageUrl($this->package));
        }
    }
}
