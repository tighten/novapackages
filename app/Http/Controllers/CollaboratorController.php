<?php

namespace App\Http\Controllers;

use App\Collaborator;

class CollaboratorController extends Controller
{
    public function index()
    {
        abort(404);

        // return view('collaborators.index')
        //     ->with('typeTags', TagResource::from(Tag::types()->orderBy('name', 'asc')->get()))
        //     ->with('popularTags', TagResource::from(Tag::nonTypes()->popular()->take(10)->get()))
        //     ->with('popularPackages', PackageResource::from(Package::popular()->take(6)->with(['author', 'ratings'])->get()))
        //     ->with('recentPackages', PackageResource::from(Package::latest()->take(6)->with(['author', 'ratings'])->get()))
        //     ->with('packages', PackageResource::from(Package::orderBy('created_at', 'desc')->with(['tags', 'author', 'ratings'])->get()));
    }

    public function show(Collaborator $collaborator)
    {
        return view('collaborators.show')
            ->with('collaborator', $collaborator);
    }
}
