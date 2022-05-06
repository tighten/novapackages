<?php

namespace App\Http\Resources;

use App\Favorite;
use App\Package;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

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
            'is_disabled' => $package->is_disabled,
            'icon_url' => $package->picture_url ?? 'https://api.adorable.io/avatars/285/'.Str::slug($package->name).'.png',
            'url' => $package->url,
            'average_rating' => $this->averageRating($package),
            'rating_count' => $this->ratingCount($package),
            'created_at' => $package->created_at->diffForHumans(),
            'author' => [
                'id' => $package->author_id,
                'name' => $package->author->name,
                'url' => $package->author->url,
                'avatar_url' => $package->author->avatar ?: 'https://api.adorable.io/avatars/285/'.Str::slug($package->author->name).'.png',
                'github_username' => $package->author->github_username,
            ],
            'nova_version' => $package->nova_version ?? null,
            'possibly_abandoned' => $this->isPossiblyAbandoned($package, $composer_latest ?? null, $packagistData ?? []),
        ];
    }

    /**
     * If a package is old *and* not on Packagist, mark it as likely abandoned
     */
    public function isPossiblyAbandoned($package, $composer_latest, $packagistData)
    {
        return Arr::get($packagistData, 'package.abandoned', false) ||
            ($package->created_at->diffInDays(now()) > 15 && ! $composer_latest);
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
}
