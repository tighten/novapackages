<?php

namespace App\Console\Commands;

use App\Models\Package;
use App\Notifications\NotifyAuthorOfDisabledPackage;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('novapackages:disable-unavailable-packages')]
#[Description('Disable unavailable packages after one month.')]
class DisableUnavailablePackages extends Command
{
    public function handle(): void
    {
        $unavailablePackages = Package::whereNotNull('marked_as_unavailable_at')
            ->where('is_disabled', 0)
            ->get();

        $unavailablePackages->each(function ($package) {
            $diffInDays = (int) abs(now()->diffInDays($package->marked_as_unavailable_at));

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
