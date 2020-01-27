<?php

namespace App\Http\Controllers;

use App\CacheKeys;
use App\Http\Resources\PackageDetailResource;
use App\Http\Resources\PackageResource;
use App\Http\Resources\TagResource;
use App\Package;
use App\Policies\PackagePolicy;
use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    const CACHE_LENGTH = 10;

    public function __invoke(Request $request)
    {
        Log::info('Web search for: '.$request->input('q'));

        return view('search')
            ->with('query', $request->input('q'))
            ->with('packages', PackageResource::from($this->searchFor($request->input('q'))));
    }

    private function searchFor($q)
    {
        // Basic search for testing when you don't have Internet
        /* return Package::orderBy('created_at', 'desc')->with(['author', 'tags'])->where('name', 'LIKE', '%' . $request->input('q') . '%')->get(); */
        return Cache::remember(CacheKeys::packageSearchResults($q), self::CACHE_LENGTH, function () use ($q) {
            return Package::search($q)->get()->load(['author', 'tags'])->values();
        });
    }
}
