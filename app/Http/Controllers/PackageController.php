<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageDetailResource;
use App\Models\Package;

class PackageController extends Controller
{
    public function index()
    {
        return view('packages.index');
    }

    public function show($namespace, $name)
    {
        $package = Package::query()
            ->where('composer_name', $namespace.'/'.$name)
            ->when(auth()->user() && auth()->user()->isAdmin(), fn ($query) => $query->withoutGlobalScopes())
            ->firstOrFail();

        return view('packages.show', [
            'package' => PackageDetailResource::from($package),
            'screenshots' => $package->screenshots,
            'packageOgImageUrl' => $package->og_image_public_url,
        ]);
    }

    public function showId(Package $package)
    {
        return redirect()->route('packages.show', [
            'namespace' => $package->composer_vendor,
            'name' => $package->composer_package,
        ]);
    }
}
