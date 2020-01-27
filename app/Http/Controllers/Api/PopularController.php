<?php

namespace App\Http\Controllers\Api;

use App\CacheKeys;
use App\Http\Controllers\Controller;
use App\Http\Resources\Package as PackageResource;
use App\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PopularController extends Controller
{
    const CACHE_LENGTH = 20;

    public function __invoke(Request $request)
    {
        return PackageResource::collection($this->popular());
    }

    private function popular()
    {
        return Cache::remember(CacheKeys::popularPackages(), self::CACHE_LENGTH, function () {
            return Package::popular()->with(['author', 'tags'])->take(10)->get();
        });
    }
}
