<?php

namespace App\Http\Livewire;

use App\Events\PackageRated;
use App\Exceptions\SelfAuthoredRatingException;
use Livewire\Component;

class RateAndReviewPackage extends Component
{
    public float $averageRating;

    public int $currentUserRating;

    public bool $isCreatingReview = false;
    // @todo:
    public bool $isSelfAuthored = false;
    // @todo:
    public bool $isSelfContributed = false;

    public int $packageId;

    public int $ratingCount;

    public array $ratingCounts;

    public string $reviewUrl;

    public bool $userHasRated = false;

    public function render()
    {
        return view('livewire.rate-and-review-package');
    }

    public function getTotalRatingCountProperty()
    {
        return collect($this->ratingCounts)->pluck('count')->sum();
    }

    public function rate($rating)
    {
        try {
            auth()->user()->ratePackage($this->packageId, $rating);
        } catch (SelfAuthoredRatingException $e) {
            return;
        }

        event(new PackageRated($this->packageId));

        $this->currentUserRating = $rating;
    }
}
