<?php

namespace App\Console\Commands;

use App\Notifications\RemindAuthorOfUnavailablePackage;
use App\Package;
use Illuminate\Console\Command;

class SendUnavailablePackageFollowUp extends Command
{

    protected $signature = 'novapackages:send-unavailable-package-followup';

    protected $description = 'If package has been unavailable for two weeks, send follow-up notice.';

    public function handle()
    {
        $invalidPackages = Package::whereNotNull('marked_as_unavailable_at')
            ->where('is_disabled', 0)
            ->get();

        $invalidPackages->each(function($package) {
            $diffInDays = now()->diffInDays($package->marked_as_unavailable_at);
            if ($diffInDays != 14) return;

            if ($package->author && $package->authorIsUser()) {
                $package->author->user->notify(new RemindAuthorOfUnavailablePackage($package));
            }
        });
    }
}
