<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Jobs\SyncPackageRepositoryData;
use App\Models\Package;

class RefreshPackageRepositoryController extends Controller
{
    public function __invoke(Package $package)
    {
        dispatch(new SyncPackageRepositoryData($package));
    }
}
