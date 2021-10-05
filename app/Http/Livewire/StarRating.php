<?php

namespace App\Http\Livewire;

use App\Events\PackageRated;
use App\Exceptions\SelfAuthoredRatingException;
use Livewire\Component;

class StarRating extends Component
{
    public $package;

    public $rating;

    public $readOnly = true;

    protected $rules = [
        'rating' => 'required',
    ];

    public function render()
    {
        return view('livewire.star-rating');
    }

    public function rate($rating)
    {
        $this->validate();

        try {
            auth()->user()->ratePackage($this->package['id'], $rating);
            event(new PackageRated(request('package_id')));

            $this->rating = $rating;
        } catch (SelfAuthoredRatingException $e) {
            return response([
                'status' => 'error',
                'message' => 'A package cannot be rated by its author',
            ], 422);
        }
    }
}
