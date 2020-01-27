<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageDetailResource;
use App\Package;

class PackageController extends Controller
{
    public function index()
    {
        return view('packages.index');
    }

    public function show($namespace, $name)
    {
        $query = Package::where('composer_name', $namespace.'/'.$name);

        if (auth()->user() && auth()->user()->isAdmin()) {
            $query = Package::withoutGlobalScopes()->where('composer_name', $namespace.'/'.$name);
        }

        $package = $query->firstOrFail();

        return view('packages.show')
            ->with('package', PackageDetailResource::from($package))
            ->with('screenshots', $package->screenshots);
    }
}
