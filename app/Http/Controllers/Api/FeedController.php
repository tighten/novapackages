<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Package;

class FeedController extends Controller
{
    public function __invoke()
    {
        return cache()->remember('all-packages-as-json', 3600, function () {
            return Package::query()
                ->with(['tags', 'author'])
                ->get()
                ->map(fn ($package) => [
                    'name' => $package->display_name,
                    'author' => $package->author->name,
                    'abstract' => $package->abstract,
                    'url' => route('packages.show', [
                        'namespace' => $package->composer_vendor,
                        'name' => $package->composer_package,
                    ]),
                    'tags' => $package->tags->pluck('name'),
                ]);
        });
    }
}
