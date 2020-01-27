<?php

namespace App\Http\Controllers\App;

use App\CacheKeys;
use App\Http\Controllers\Controller;
use App\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PackagePackagistCacheController extends Controller
{
    public function destroy(Package $package)
    {
        Cache::forget(CacheKeys::packagistData($package->composer_name));

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success']);
        } else {
            return redirect()->back();
        }
    }
}
