<?php

namespace App;

class CacheKeys
{
    public static function averageRating($class, $id)
    {
        return 'average-rating--model::' . $class . '-id::' . $id;
    }

    public static function userPackageRating($userId, $packageId)
    {
        return 'user-package-rating::package-' . $packageId . '-user-' . $userId;
    }

    public static function ratingsCounts($class, $id)
    {
        return 'count-ratings--model::' . $class . '-id::' . $id . '-count';
    }

    public static function packageSearchResults($term)
    {
        return 'package-search-results::' . $term;
    }

    public static function packagistData($packageFullComposerString)
    {
        return 'packagist-' . $packageFullComposerString;
    }

    public static function popularPackages()
    {
        return 'popular-packages';
    }

    public static function popularTags()
    {
        return 'popular-tags';
    }

    public static function recentPackages()
    {
        return 'recent-packages';
    }

    public static function packagesCount()
    {
        return 'packages-count';
    }

    public static function packagistDownloadsCount()
    {
        return 'packagist-downloads-count';
    }

    public static function githubStarsCount()
    {
        return 'github-stars-count';
    }

    public static function ratingsCount()
    {
        return 'ratings-count';
    }

    public static function collaboratorsCount()
    {
        return 'collaborators-count';
    }

    public static function globalAverageRating()
    {
        return 'average-package-rating';
    }

    public static function novaReleases()
    {
        return 'nova-releases-api-response';
    }

    public static function packageIdeaIssues()
    {
        return 'github-issues--package-idea';
    }

    public static function githubReadme($packageName)
    {
        return 'github-readme::' . $packageName;
    }

    public static function npmData($packageName)
    {
        return 'npm-' . $packageName;
    }
}
