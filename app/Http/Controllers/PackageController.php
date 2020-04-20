<?php

namespace App\Http\Controllers;

use App\Package;
use Illuminate\Support\Str;
use App\Jobs\GeneratePackageOpenGraphImage;
use App\Http\Resources\PackageDetailResource;

class PackageController extends Controller
{
    public function index()
    {
        return view('packages.index');
    }

    public function show($namespace, $name)
    {
        $query = Package::where('composer_name', $namespace . '/' . $name);

        if (auth()->user() && auth()->user()->isAdmin()) {
            $query = Package::withoutGlobalScopes()->where('composer_name', $namespace . '/' . $name);
        }

        $package = $query->firstOrFail();

        dispatch(new GeneratePackageOpenGraphImage($package->name, $package->author->name));

        $packageOgImage = Str::slug($package->name, '-') . '.png';

        return view('packages.show', [
            'package' => PackageDetailResource::from($package),
            'screenshots' => $package->screenshots,
            'packageOgImage' => $packageOgImage,
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
