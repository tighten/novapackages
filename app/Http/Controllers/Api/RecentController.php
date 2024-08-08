<?php

namespace App\Http\Controllers\Api;

use App\CacheKeys;
use App\Http\Controllers\Controller;
use App\Http\Resources\Package as PackageResource;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RecentController extends Controller
{
    const CACHE_LENGTH = 10;

    public function __invoke(Request $request)
    {
        Log::info('API: /recent');

        return PackageResource::collection($this->recent());
    }

    private function recent()
    {
        return Cache::remember(CacheKeys::recentPackages(), self::CACHE_LENGTH, function () {
            return Package::latest()->with(['author', 'tags'])->take(10)->get();
        });
    }
}
