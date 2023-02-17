<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Package as PackageResource;
use App\Models\Package;
use Illuminate\Http\Request;

class PackagesController extends Controller
{
    public function __invoke(Request $request)
    {
        $githubUsername = $request->input('github_username');
        $authorName = $request->input('author_name');

        $packages = Package::query()
            ->with(['author', 'tags'])
            ->when($githubUsername, function ($query, $githubUsername) {
                $query->whereHas('author', function ($query) use ($githubUsername) {
                    $query->where('github_username', $githubUsername);
                });
            })
            ->when($authorName, function ($query, $authorName) {
                $query->whereHas('author', function ($query) use ($authorName) {
                    $query->where('name', $authorName);
                });
            })
            ->latest()
            ->paginate(10);

        return PackageResource::collection($packages);
    }
}
