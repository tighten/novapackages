<?php

namespace App\Console\Commands;

use App\Jobs\CheckPackageUrls as CheckPackageUrlsJob;
use App\Package;
use Illuminate\Console\Command;

class CheckPackageUrls extends Command
{

    protected $signature = 'check:package-urls';

    protected $description = 'Check all package URLs for 404 errors';

    public function handle()
    {
        $validPackages = Package::whereNull('marked_as_unavailable_at')
            ->with(['author', 'contributors'])
            ->get();

        foreach ($validPackages as $package) {
            dispatch(new CheckPackageUrlsJob($package));
        }
    }
}
