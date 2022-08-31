<?php

namespace App\Http\Livewire;

use App\Jobs\SyncPackageRepositoryData;
use App\Package;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class RequestRepositoryRefresh extends Component
{
    use AuthorizesRequests;

    public int $packageId;
    public bool $refreshRequested = false;

    public function render()
    {
        return view('livewire.request-repository-refresh');
    }

    public function requestRefresh()
    {
        $this->authorize('update', Package::findOrFail($this->packageId));

        dispatch(new SyncPackageRepositoryData(Package::findOrFail($this->packageId)));

        $this->refreshRequested = true;
    }
}
