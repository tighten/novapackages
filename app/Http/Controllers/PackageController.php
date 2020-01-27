<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageDetailResource;
use App\Http\Resources\PackageResource;
use App\Http\Resources\TagResource;
use App\Package;
use App\Tag;

class PackageController extends Controller
{
    public function index()
    {
        return view('welcome')
            ->with('typeTags', TagResource::from(Tag::types()->orderBy('name', 'asc')->get()))
            ->with('popularTags', TagResource::from(Tag::popular()->take(10)->get()->sortByDesc('packages_count')))
            ->with('popularPackages', PackageResource::from(Package::popular()->take(6)->with(['author', 'ratings', 'tags'])->withCount('favorites')->get()))
            ->with('recentPackages', PackageResource::from(Package::latest()->take(3)->with(['author', 'ratings', 'tags'])->withCount('favorites')->get()))
            ->with('packages', PackageResource::from(Package::orderBy('created_at', 'desc')->with(['tags', 'author', 'ratings'])->withCount('favorites')->get()));
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

    public function redirectOldRoutes($packageId)
    {
        $package = Package::findOrFail($packageId);

        return redirect()->route('packages.show', [
            'namespace' => $package->composer_vendor,
            'name' => $package->composer_package,
        ]);
    }
}
