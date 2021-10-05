<?php

namespace App\Http\Livewire;

use Livewire\Component;

class StarRating extends Component
{
    public $rating;

    public $readOnly = true;

    public function render()
    {
        return view('livewire.star-rating');
    }

    public function rate($rating)
    {
        // todo
    }
}
