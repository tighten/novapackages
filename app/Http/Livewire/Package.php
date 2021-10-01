<?php

namespace App\Http\Livewire;

use App\CacheKeys;
use App\Jobs\SyncPackageRepositoryData;
use App\Package as EloquentPackage;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Package extends Component
{
    public $package;

    public $packagistRefreshRequested = false;

    public $repositoryRefreshRequested = false;

    public $showInstallDropdown = false;

    public function render()
    {
        return view('livewire.package');
    }

    public function requestPackagistRefresh()
    {
        $this->packagistRefreshRequested = true;
        Cache::forget(CacheKeys::packagistData($this->package['composer_name']));
    }

    public function requestRepositoryRefresh()
    {
        $this->repositoryRefreshRequested = true;
        dispatch(new SyncPackageRepositoryData(EloquentPackage::find($this->package['id'])));
    }

    public function favorite()
    {
        if ($this->package['is_favorite']) {
            auth()->user()->unfavoritePackage($this->package['id']);
            $this->package['is_favorite'] = false;
            $this->package['favorites_count']--;
            return;
        }

        auth()->user()->favoritePackage($this->package['id']);
        $this->package['is_favorite'] = true;
        $this->package['favorites_count']++;
    }

    public function rate($rating)
    {
        // todo
    }
}
