<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Package;

class PackageReviewController extends Controller
{
    public function create($namespace, $name): View
    {
        $package = Package::where('composer_name', $namespace . '/' . $name)->firstOrFail();

        return view('package-reviews.create', [
            'package' => $package,
        ]);
    }
}
