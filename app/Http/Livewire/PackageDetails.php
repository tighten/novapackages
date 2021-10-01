<?php

namespace App\Http\Livewire;

use Livewire\Component;

class PackageDetails extends Component
{
    public $package;

    public $screenshots;

    public function render()
    {
        return view('livewire.package-details');
    }
}
