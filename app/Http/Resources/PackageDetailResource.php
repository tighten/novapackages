<?php

namespace App\Http\Resources;

use App\CacheKeys;
use App\Favorite;
use App\Http\Resources\TagResource;
use App\ReadmeFormatter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PackageDetailResource extends PackageResource
{
    public function toArray($package)
    {
        $packagistData = $package->fetchPackagistData();
        $composer_latest = $package->fetchLatestStableVersion($packagistData);

        return array_merge(parent::toArray($package), [
            'composer_data' => $packagistData ?? false,
            'composer_latest' => $composer_latest,
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
            // reload possibly_abandoned with the mutator from the class ($package->is_abandoned)
            'possibly_abandoned' => $package->isPossiblyAbandoned(),
            'marked_as_unavailable_at' => $package->marked_as_unavailable_at,
            'github_stars' => $package->github_stars,
            'packagist_downloads' => $package->packagist_downloads,
            'tags' => TagResource::from($package->tags),
            'is_favorite' => $this->isFavorite($package),
            'favorites_count' => $this->favoritesCount($package),
        ]);
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
            return (int)$package->user_average_rating;
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
