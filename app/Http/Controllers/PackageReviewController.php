<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageDetailResource;
use App\Package;

class PackageReviewController extends Controller
{
    public function create($namespace, $name)
    {
        $package = Package::where('composer_name', $namespace.'/'.$name)->firstOrFail();
        $userStarRating = $package->ratings->where('user_id', auth()->id())->first();

        return view('new-review')->with([
            'package' => PackageDetailResource::from($package),
            'userStarRating' => $userStarRating,
        ]);
    }
}
