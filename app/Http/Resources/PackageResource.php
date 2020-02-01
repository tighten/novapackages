<?php

namespace App\Http\Resources;

use App\CacheKeys;
use App\Favorite;
use App\Http\Resources\TagResource;
use App\Package;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PackageResource extends ModelResource
{
    public $model = Package::class;

    const CACHE_RATINGS_LENGTH = 5;

    public function toArray($package)
    {
        return [
            'id' => $package->id,
            'name' => $package->name,
            'composer_name' => $package->composer_name,
            'packagist_namespace' => $package->composer_vendor,
            'packagist_name' => $package->composer_package,
            'abstract' => $package->abstract,
            'tags' => TagResource::from($package->tags),
            'is_disabled' => $package->is_disabled,
            'icon_url' => $package->picture_url ?? 'https://api.adorable.io/avatars/285/'.Str::slug($package->name).'.png',
            'url' => $package->url,
            'average_rating' => $this->averageRating($package),
            'rating_count' => $this->ratingCount($package),
            'created_at' => $package->created_at->diffForHumans(),
            'is_favorite' => $this->isFavorite($package),
            'favorites_count' => $this->favoritesCount($package),
            'author' => [
                'name' => $package->author->name,
                'url' => $package->author->url,
                'avatar_url' => $package->author->avatar ?: 'https://api.adorable.io/avatars/285/'.Str::slug($package->author->name).'.png',
                'github_username' => $package->author->github_username,
            ],
        ];
    }

    protected function averageRating($package)
    {
        return number_format($package->average_rating, '2', '.', '');
    }

    protected function ratingCount($package)
    {
        if (isset($package->ratings_count)) {
            return $package->ratings_count;
        }

        if ($package->relationLoaded('ratings')) {
            return $package->ratings->count();
        }

        return $package->ratings()->count();
    }

    protected function isFavorite($package)
    {
        return auth()->user() && (auth()->user()->favorites()->where('package_id', $package->id)->count() > 0);
    }

    protected function favoritesCount($package)
    {
        return $package->favorites_count ?? Favorite::where('package_id', $package->id)->count();
    }
}
