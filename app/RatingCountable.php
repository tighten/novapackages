<?php

namespace App;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait RatingCountable
{
    public $ratingsCountCacheLength = 60;

    public $averageRatingCacheLength = 60;

    public function countStarRatings($numberOfStars)
    {
        return $this->getRatingsCounts()[$numberOfStars];
    }

    protected function getRatingsCounts()
    {
        return Cache::remember(
            CacheKeys::ratingsCounts(static::class, $this->id),
            $this->ratingsCountCacheLength,
            function () {
                if ($this->relationLoaded('ratings')) {
                    $ratings = $this->ratingsCountsFromEagerLoad();
                } else {
                    $ratings = $this->ratingsCountsFromRawDb();
                }

                return array_replace(
                    [
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    ],
                    $ratings->toArray()
                );
            }
        );
    }

    public function countOneStarRatings()
    {
        return $this->countStarRatings(1);
    }

    public function countTwoStarRatings()
    {
        return $this->countStarRatings(2);
    }

    public function countThreeStarRatings()
    {
        return $this->countStarRatings(3);
    }

    public function countFourStarRatings()
    {
        return $this->countStarRatings(4);
    }

    public function countFiveStarRatings()
    {
        return $this->countStarRatings(5);
    }

    protected function ratingsCountsFromEagerLoad()
    {
        return $this->ratings->groupBy('rating')->map(fn ($ratings) => $ratings->count());
    }

    protected function ratingsCountsFromRawDb()
    {
        return $this->ratings()
            ->groupBy('rating')
            ->select(DB::Raw('count(id) as count, rating'))
            ->get()
            ->mapWithKeys(fn ($ratingCount) => [$ratingCount->rating => $ratingCount->count]);
    }

    /**
     * Override the Rateable method to take advantage of our cache.
     *
     * @todo  maybe check if ratings is counted; if so, use it; if not, default to the other one?
     */
    public function averageRating()
    {
        return Cache::remember(
            CacheKeys::averageRating(static::class, $this->id),
            $this->averageRatingCacheLength,
            function () {
                $ratingsCounts = collect($this->getRatingsCounts());

                if ($ratingsCounts->sum() === 0) {
                    return 0;
                }

                $num = $ratingsCounts
                    ->map(fn ($count, $stars) => $count * $stars)
                    ->sum() / $ratingsCounts->sum();

                return round($num, 1);
            }
        );
    }
}
