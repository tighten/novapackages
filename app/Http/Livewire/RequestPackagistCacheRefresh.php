<?php

namespace App\Http\Livewire;

use App\CacheKeys;
use App\Package;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class RequestPackagistCacheRefresh extends Component
{
    use AuthorizesRequests;

    public string $composerName;
    public int $packageId;
    public bool $refreshRequested = false;

    public function render()
    {
        return view('livewire.request-packagist-cache-refresh');
    }

    public function requestRefresh()
    {
        $this->authorize('update', Package::findOrFail($this->packageId));

        Cache::forget(CacheKeys::packagistData($this->composerName));

        $this->refreshRequested = true;
    }
}
