<?php

namespace App\Console\Commands;

use App\Notifications\NotifyAuthorOfDisabledPackage;
use App\Package;
use Illuminate\Console\Command;

class DisableUnavailablePackages extends Command
{

    protected $signature = 'novapackages:disable-unavailable-packages';

    protected $description = 'Disable unavailable packages after one month.';

    public function handle()
    {
        $unavailablePackages = Package::whereNotNull('marked_as_unavailable_at')
            ->where('is_disabled', 0)
            ->get();

        $unavailablePackages->each(function ($package) {
            $diffInDays = now()->diffInDays($package->marked_as_unavailable_at);
            if ($diffInDays < 30) {
                return;
            }

            $package->is_disabled = 1;
            $package->save();
            $this->info("{$package->name} has been disabled.");

            if ($package->author && $package->authorIsUser()) {
                $package->author->user->notify(new NotifyAuthorOfDisabledPackage($package));
            }
        });
    }
}
