<?php

namespace App\Http\Controllers\Api;

use Algolia\AlgoliaSearch\SearchIndex;
use App\CacheKeys;
use App\Http\Controllers\Controller;
use App\Http\Resources\Package as PackageResource;
use App\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    const CACHE_LENGTH = 10;

    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'q' => 'required|min:3',
        ]);

        Log::info('API search for: '.$request->input('q'));

        return PackageResource::collection($this->searchFor($request->input('q')));
    }

    private function searchFor($q)
    {
        return Cache::remember(CacheKeys::packageSearchResults($q), self::CACHE_LENGTH, function () use ($q) {
            return Package::search($q, function (SearchIndex $algolia, string $query, array $options) {
                $options['advancedSyntax'] = true;
                return $algolia->search($query, $options);
            })->get()->load(['tags', 'author']);
        });
    }
}
