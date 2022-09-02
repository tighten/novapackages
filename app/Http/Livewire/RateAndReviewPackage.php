<?php

namespace App\Http\Livewire;

use Livewire\Component;

class RateAndReviewPackage extends Component
{
    public float $averageRating;

    public bool $isCreatingReview = false;
    // @todo:
    public bool $isSelfAuthored = false;
    // @todo:
    public bool $isSelfContributed = false;

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
}
