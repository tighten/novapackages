<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageResource;
use App\Http\Resources\TagResource;
use App\Package;
use App\Tag;

class WelcomeController extends Controller
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
}
