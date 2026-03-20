<?php

namespace App\Console\Commands;

use App\Jobs\SyncPackagePackagistData;
use App\Models\Package;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('sync:packagist')]
#[Description('Cache Packagist download counts and GitHub stars for every package.')]
class SyncPackagistData extends Command
{
    public function handle(): void
    {
        foreach (Package::all() as $package) {
            dispatch(new SyncPackagePackagistData($package));
        }
    }
}
