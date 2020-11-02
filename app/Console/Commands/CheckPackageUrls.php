<?php

namespace App\Console\Commands;

use App\Jobs\CheckPackageUrlsForAvailability as CheckPackageUrlsJob;
use App\Package;
use Illuminate\Console\Command;

class CheckPackageUrls extends Command
{
    protected $signature = 'novapackages:check-package-urls';

    protected $description = 'Check all package URLs for 4XX errors';

    public function handle()
    {
        Package::whereNull('marked_as_unavailable_at')
            ->get()
            ->each(function ($package) {
                dispatch(new CheckPackageUrlsJob($package));
            });
    }
}
