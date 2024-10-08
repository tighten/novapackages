<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Http\Resources\PackageDetailResource;
use App\Package;

class PackageReviewController extends Controller
{
    public function create($namespace, $name): View
    {
        $package = Package::where('composer_name', $namespace . '/' . $name)->firstOrFail();
        $userStarRating = $package->ratings->where('user_id', auth()->id())->first();

        return view('package-reviews.create', [
            'package' => PackageDetailResource::from($package),
            'userStarRating' => $userStarRating,
        ]);
    }
}
