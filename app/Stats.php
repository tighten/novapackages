<?php

namespace App;

use App\Models\Collaborator;
use App\Models\Package;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use willvincent\Rateable\Rating;

class Stats
{
    const CACHE_LENGTH = 20;

    public function packageCount()
    {
        return Cache::remember(CacheKeys::packagesCount(), self::CACHE_LENGTH, function () {
            return Package::count();
        });
    }

    public function packagistDownloadsCount()
    {
        return Cache::remember(CacheKeys::packagistDownloadsCount(), self::CACHE_LENGTH, function () {
            return Package::sum('packagist_downloads');
        });
    }

    public function githubStarsCount()
    {
        return Cache::remember(CacheKeys::githubStarsCount(), self::CACHE_LENGTH, function () {
            return Package::sum('github_stars');
        });
    }

    public function novaLatestVersion()
    {
        $data = Cache::remember(CacheKeys::novaReleases(), self::CACHE_LENGTH, function () {
            return Http::get('https://nova.laravel.com/api/releases')->json();
        });

        return 'v'.$data['current_version'];
    }

    public function collaboratorsCount()
    {
        return Cache::remember(CacheKeys::collaboratorsCount(), self::CACHE_LENGTH, function () {
            return Collaborator::count();
        });
    }

    public function ratingsCount()
    {
        return Cache::remember(CacheKeys::ratingsCount(), self::CACHE_LENGTH, function () {
            return Rating::count();
        });
    }

    public function globalAverageRating()
    {
        return Cache::remember(CacheKeys::globalAverageRating(), self::CACHE_LENGTH, function () {
            return Rating::avg('rating');
        });
    }
}
