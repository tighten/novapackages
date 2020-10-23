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
        $validPackages = Package::whereHas('tags', function ($query) {
            $query->where('name', '!=', '404 error');
        })
        ->orWhereDoesntHave('tags')
        ->with(['author', 'contributors'])
        ->get();

        foreach ($validPackages as $package) {
            dispatch(new CheckPackageUrlsJob($package));
        }
    }
}
