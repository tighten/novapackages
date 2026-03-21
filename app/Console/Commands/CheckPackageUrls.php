<?php

namespace App\Console\Commands;

use App\Jobs\CheckPackageUrlsForAvailability as CheckPackageUrlsJob;
use App\Models\Package;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('novapackages:check-package-urls')]
#[Description('Check all package URLs for 4XX errors')]
class CheckPackageUrls extends Command
{
    public function handle(): void
    {
        $validPackages = Package::whereNull('marked_as_unavailable_at')
            ->with('author')
            ->get();

        foreach ($validPackages as $package) {
            dispatch(new CheckPackageUrlsJob($package));
        }
    }
}
