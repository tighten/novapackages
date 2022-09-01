<?php

namespace App\Http\Livewire;

use Livewire\Component;

class PackageFavorites extends Component
{
    public int $favoriteCount;
    public bool $isFavorite;
    public int $packageId;

    public function render()
    {
        return view('livewire.package-favorites');
    }

    public function toggleFavorite()
    {
        auth()->authenticate();

        if ($this->isFavorite) {
            $this->unfavoritePackage();
        } else {
            $this->favoritePackage();
        }
    }

    private function favoritePackage(): void
    {
        auth()->user()->favoritePackage($this->packageId);
        $this->isFavorite = true;
        $this->favoriteCount++;
    }

    private function unfavoritePackage(): void
    {
        auth()->user()->unfavoritePackage($this->packageId);
        $this->isFavorite = false;
        $this->favoriteCount--;
    }
}
