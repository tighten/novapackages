<?php

namespace App\Livewire;

use App\Events\PackageRated;
use App\Exceptions\SelfAuthoredRatingException;
use App\Package;
use Livewire\Component;
use willvincent\Rateable\Rating;

class PackageReviewCreate extends Component
{
    public int $packageId;
    public int $rating = 0;
    public string $content = '';
    public bool $rated = false;

    public function mount(int $packageId, ?int $initialRating = null)
    {
        $this->packageId = $packageId;

        if ($initialRating) {
            $this->rating = $initialRating;
            $this->rated = true;
        }
    }

    public function setRating(int $stars)
    {
        try {
            auth()->user()->ratePackage($this->packageId, $stars);
        } catch (SelfAuthoredRatingException $e) {
            $this->dispatch('toast', message: 'A package cannot be rated by its author.', type: 'error');

            return;
        }

        event(new PackageRated($this->packageId));

        $this->rating = $stars;
        $this->rated = true;
    }

    public function submit()
    {
        $this->validate([
            'content' => 'required|min:20|max:5000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $package = Package::findOrFail($this->packageId);

        $ratingRecord = Rating::where('rateable_id', $this->packageId)
            ->where('user_id', auth()->id())
            ->first();

        $package->addReview($ratingRecord->id, $this->content);

        session()->flash('status', 'Review submitted successfully.');

        return redirect()->route('packages.show', [
            'namespace' => $package->composer_vendor,
            'name' => $package->composer_package,
        ]);
    }

    public function render()
    {
        return view('livewire.package-review-create');
    }
}
