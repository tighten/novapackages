<?php

namespace App\Console\Commands;

use App\Jobs\SyncPackagePackagistData;
use App\Package;
use Illuminate\Console\Command;

class SyncPackagistData extends Command
{
    protected $signature = 'sync:packagist';

    protected $description = 'Cache Packagist download counts and GitHub stars for every package.';

    public function handle()
    {
        foreach (Package::all() as $package) {
            dispatch(new SyncPackagePackagistData($package));
        }
    }
}
