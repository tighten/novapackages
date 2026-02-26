<?php

namespace App\Console\Commands;

use App\Models\Package;
use App\Notifications\RemindAuthorOfUnavailablePackage;
use Illuminate\Console\Command;

class SendUnavailablePackageFollowUp extends Command
{
    protected $signature = 'novapackages:send-unavailable-package-followup';

    protected $description = 'If package has been unavailable for two weeks, send follow-up to package author.';

    public function handle(): void
    {
        $unavailablePackages = Package::whereNotNull('marked_as_unavailable_at')
            ->where('is_disabled', 0)
            ->get();

        $unavailablePackages->each(function ($package) {
            $diffInDays = (int) abs(now()->diffInDays($package->marked_as_unavailable_at));
            if ($diffInDays != 14) {
                return;
            }

            if ($package->author && $package->authorIsUser()) {
                $package->author->user->notify(new RemindAuthorOfUnavailablePackage($package));
            }
        });
    }
}
