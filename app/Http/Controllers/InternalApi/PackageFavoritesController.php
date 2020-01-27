<?php

namespace App\Http\Controllers\InternalApi;

use App\Http\Controllers\Controller;
use App\Package;

class PackageFavoritesController extends Controller
{
    public function store(Package $package)
    {
        auth()->user()->favoritePackage($package->id);

        return response(['status' => 'success', 'message' => 'Favorite created successfully'], 201);
    }

    public function destroy(Package $package)
    {
        auth()->user()->unfavoritePackage($package->id);

        return response(['status' => 'success', 'message' => 'Favorite removed successfully'], 200);
    }
}
