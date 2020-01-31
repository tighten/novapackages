<?php

namespace App\Jobs;

use App\Exceptions\SelfAuthoredRatingException;
use App\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use willvincent\Rateable\Rating;

class UserRatePackage implements ShouldQueue
{
    private $user;

    private $package;

    private $stars;

    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct($userId, $packageId, $stars)
    {
        $this->userId = $userId;
        $this->packageId = $packageId;
        $this->stars = $stars;
    }

    public function handle()
    {
        if (Rating::where([
            'user_id' => $this->userId,
            'rateable_type' => Package::class,
            'rateable_id' => $this->packageId,
        ])->count() === 0) {
            $rating = new Rating;
            $rating->rating = $this->stars;
            $rating->user_id = $this->userId;

            $package = Package::findOrFail($this->packageId);

            if ($this->isSelfAuthored($package) || $this->isSelfContributed($package)) {
                throw new SelfAuthoredRatingException;
            }

            $package->ratings()->save($rating);
        } else {
            $rating = Rating::where([
                'user_id' => $this->userId,
                'rateable_type' => Package::class,
                'rateable_id' => $this->packageId,
            ])->first();

            $rating->rating = $this->stars;
            $rating->save();
        }
    }

    private function isSelfAuthored($package)
    {
        return (int) $package->author_id === (int) $this->userId;
    }

    private function isSelfContributed($package)
    {
        return $package->contributors->pluck('user_id')->contains($this->userId);
    }
}
