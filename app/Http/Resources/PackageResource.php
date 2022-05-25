<?php

namespace App\Http\Resources;

use App\Package;
use App\Favorite;
use Illuminate\Support\Str;
use App\Helpers\TrimPackageName;

class PackageResource extends ModelResource
{
    public $model = Package::class;

    const CACHE_RATINGS_LENGTH = 5;

    public function toArray($package)
    {
        return [
            'id' => $package->id,
            'name' => TrimPackageName::trim($package->name),
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
}
