<?php

namespace App;

use App\Models\Rating;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

trait Rateable
{
    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function rate($value, $comment = null, $user_id = null)
    {
        $rating = new Rating;
        $rating->rating = $value;
        $rating->comment = $comment;
        $rating->user_id = $user_id ?: Auth::id();

        $this->ratings()->save($rating);
    }

    public function rateOnce($value, $comment = null, $user_id = null)
    {
        $user_id = $user_id ?: Auth::id();

        $rating = Rating::query()
            ->where('rateable_type', '=', $this->getMorphClass())
            ->where('rateable_id', '=', $this->id)
            ->where('user_id', '=', $user_id)
            ->first();

        if ($rating) {
            $rating->rating = $value;
            $rating->comment = $comment;
            $rating->save();
        } else {
            $this->rate($value, $comment, $user_id);
        }
    }

    public function averageRating()
    {
        return $this->ratings()->avg('rating');
    }

    public function sumRating()
    {
        return $this->ratings()->sum('rating');
    }

    public function timesRated()
    {
        return $this->ratings()->count();
    }

    public function usersRated()
    {
        return $this->ratings()->groupBy('user_id')->pluck('user_id')->count();
    }

    public function userAverageRating($user_id = null)
    {
        $user_id = $user_id ?: Auth::id();

        return $this->ratings()->where('user_id', $user_id)->avg('rating');
    }

    public function userSumRating($user_id = null)
    {
        $user_id = $user_id ?: Auth::id();

        return $this->ratings()->where('user_id', $user_id)->sum('rating');
    }

    public function ratingPercent($max = 5)
    {
        $quantity = $this->ratings()->count();
        $total = $this->sumRating();

        return ($quantity * $max) > 0 ? $total / (($quantity * $max) / 100) : 0;
    }

    public function getAverageRatingAttribute()
    {
        return $this->averageRating();
    }

    public function getSumRatingAttribute()
    {
        return $this->sumRating();
    }

    public function getUserAverageRatingAttribute()
    {
        return $this->userAverageRating();
    }

    public function getUserSumRatingAttribute()
    {
        return $this->userSumRating();
    }
}
