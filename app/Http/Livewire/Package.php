<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Package extends Component
{
    public $package;

    public function render()
    {
        return view('livewire.package');
    }
}
