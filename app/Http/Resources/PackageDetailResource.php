<?php

namespace App\Http\Resources;

use App\CacheKeys;
use App\Exceptions\PackagistException;
use App\Favorite;
use App\Http\Remotes\Packagist;
use App\Http\Resources\TagResource;
use App\ReadmeFormatter;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PackageDetailResource extends PackageResource
{
    public function toArray($package)
    {
        try {
            $packagistData = Packagist::make($package->composer_name)->data();

            if (! is_null($packagistData)) {
                $composer_latest = $this->extractStableVersionsFromPackages($packagistData)->first();

                $novaVersion = $composer_latest['require']['laravel/nova'] ?? null;
            }
        } catch (PackagistException $e) {
        }

        return array_merge(parent::toArray($package), [
            'composer_data' => $packagistData ?? false,
            'composer_latest' => $composer_latest ?? null,
            'description' => $this->renderedText($package, 'description'),
            'readme' => $this->renderedText($package, 'readme'),
            'instructions' => $package->instructions ? markdown($package->instructions) : null,
            'current_user_rating' => $this->userRating($package),
            'current_user_review' => $this->userReview($package),
            'current_user_owns' => $this->userOwns($package),
            'rating_counts' => [
                ['number' => 5, 'count' => $package->countStarRatings(5)],
                ['number' => 4, 'count' => $package->countStarRatings(4)],
                ['number' => 3, 'count' => $package->countStarRatings(3)],
                ['number' => 2, 'count' => $package->countStarRatings(2)],
                ['number' => 1, 'count' => $package->countStarRatings(1)],
            ],
            'reviews' => $package->reviews()->with('user')->get() ?? null,
            'ratings' => $package->ratings ?? null,
            'contributors' => $package->contributors->map(function ($contributor) {
                return [
                    'id' => $contributor->user_id,
                    'name' => $contributor->name,
                    'avatar_url' => $contributor->avatar ?: 'https://api.adorable.io/avatars/285/' . Str::slug($contributor->name) . '.png',
                ];
            })->toArray(),
            'possibly_abandoned' => $this->isPossiblyAbandoned($package, $composer_latest ?? null, $packagistData ?? []),
            'marked_as_unavailable_at' => $package->marked_as_unavailable_at,
            'github_stars' => $package->github_stars,
            'packagist_downloads' => $package->packagist_downloads,
            'tags' => TagResource::from($package->tags),
            'is_favorite' => $this->isFavorite($package),
            'favorites_count' => $this->favoritesCount($package),
            'nova_version' => $novaVersion ?? null,
        ]);
    }

    /**
     * If a package is old *and* not on Packagist, mark it as likely abandoned
     */
    public function isPossiblyAbandoned($package, $composer_latest, $packagistData)
    {
        return Arr::get($packagistData, 'package.abandoned', false) ||
            ($package->created_at->diffInDays(now()) > 15 && ! $composer_latest);
    }

    protected function isFavorite($package)
    {
        return auth()->user() && (auth()->user()->favorites()->where('package_id', $package->id)->count() > 0);
    }

    protected function favoritesCount($package)
    {
        return $package->favorites_count ?? Favorite::where('package_id', $package->id)->count();
    }

    private function userRating($package)
    {
        if (auth()->guest()) {
            return;
        }

        $key = CacheKeys::userPackageRating(auth()->id(), $package->id);

        return Cache::remember($key, self::CACHE_RATINGS_LENGTH, function () use ($package) {
            return (int) $package->user_average_rating;
        });
    }

    private function extractStableVersionsFromPackages($packagist)
    {
        return collect($packagist['package']['versions'])->reject(function ($version) {
            return strpos($version['version'], 'dev') !== false;
        });
    }

    private function userOwns($package)
    {
        return auth()->user() && auth()->user()->can('update', $package);
    }

    private function renderedText($package, $attribute)
    {
        if (! $package->{$attribute}) {
            return;
        }

        return (new ReadmeFormatter($package))->format($package->{$attribute});
    }

    private function userReview($package)
    {
        return $package->reviews->where('user_id', auth()->id());
    }
}
