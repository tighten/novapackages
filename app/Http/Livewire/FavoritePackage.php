<?php

namespace App\Http\Livewire;

use Livewire\Component;

class FavoritePackage extends Component
{
    public bool $isFavorite;
    public int $packageId;

    public function render()
    {
        return view('livewire.favorite-package');
    }

    public function toggleFavorite()
    {
        auth()->authenticate();

        if ($this->isFavorite) {
            auth()->user()->unfavoritePackage($this->packageId);
            $this->isFavorite = false;
        } else {
            auth()->user()->favoritePackage($this->packageId);
            $this->isFavorite = true;
        }
    }
}
