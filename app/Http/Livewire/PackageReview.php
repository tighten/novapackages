<?php

namespace App\Http\Livewire;

use Livewire\Component;

class PackageReview extends Component
{
    public $isEditing = false;

    public $review;

    protected $rules = [
        'review.content' => 'required|string',
    ];

    public function render()
    {
        return view('livewire.package-review');
    }

    public function update()
    {
        $this->validate();

        $this->review->save();
    }

    public function delete()
    {
        $this->review->delete();
    }
}
