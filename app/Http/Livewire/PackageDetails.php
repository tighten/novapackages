<?php

namespace App\Http\Livewire;

use App\Package as EloquentPackage;
use Livewire\Component;

class PackageDetails extends Component
{
    public $package;

    public $screenshots;

    public function render()
    {
        return view('livewire.package-details');
    }

    public function disablePackage()
    {
        tap(EloquentPackage::find($this->package['id']), function ($package) {
            $package->is_disabled = true;
            $package->save();

            $this->package['is_disabled'] = true;
            $this->emit('packageDisabled');
        });
    }

    public function enablePackage()
    {
        tap(EloquentPackage::withoutGlobalScope('notDisabled')->find($this->package['id']), function ($package) {
            $package->is_disabled = false;
            $package->save();

            $this->package['is_disabled'] = false;
            $this->emit('packageEnabled');
        });
    }
}
