<?php

namespace App\Http\Controllers\InternalApi;

use App\Http\Controllers\Controller;
use App\Package;
use Illuminate\Http\Response;

class PackageFavoritesController extends Controller
{
    public function store(Package $package): Response
    {
        auth()->user()->favoritePackage($package->id);

        return response(['status' => 'success', 'message' => 'Favorite created successfully'], 201);
    }

    public function destroy(Package $package): Response
    {
        auth()->user()->unfavoritePackage($package->id);

        return response(['status' => 'success', 'message' => 'Favorite removed successfully'], 200);
    }
}
