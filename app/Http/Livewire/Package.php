<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Package extends Component
{
    public $package;

    // @todo: Use entangle for the following and to hide dropdown on load
    // https://laravel-livewire.com/docs/2.x/alpine-js
    public $packagistRefreshRequested = false;

    public $repositoryRefreshRequested = false;

    public function render()
    {
        return view('livewire.package');
    }

    public function requestPackagistRefresh()
    {

    }

    public function requestRepositoryRefresh()
    {

    }
}
