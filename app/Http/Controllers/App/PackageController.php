<?php

namespace App\Http\Controllers\App;

use App\Collaborator;
use App\Events\PackageCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\PackageFormRequest;
use App\Package;
use App\Tag;
use DateTime;
use Facades\App\Repo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    public function index()
    {
        return view('app.packages.index', [
            'packages' => Package::get(),
            'favoritePackages' => auth()->user()->favoritePackages(),
        ]);
    }

    public function create()
    {
        return view('app.packages.create', [
            'collaborators' => Collaborator::orderBy('name')->get(),
            'tags' => Tag::orderBy('slug')->get(),
        ]);
    }

    public function store(PackageFormRequest $request)
    {
        $repo = Repo::fromRequest($request);

        // @todo: Kick off a sync operation and validate it's a real repo? grab name? geez there's a lot here that's sillly to make them enter manually
        $package = Package::create(array_merge(
            request()->only(['name', 'author_id', 'url', 'abstract', 'instructions']),
            [
                'composer_name' => $request->getComposerName(),
                'repo_url' => $repo->url(),
                'readme_source' => $repo->source(),
                'readme_format' => $repo->readmeFormat(),
                'submitter_id' => auth()->user()->id,
                'readme' => $repo->readme(),
                'latest_version' => $repo->latestReleaseVersion(),
            ]
        ));

        $package->contributors()->sync(request()->input('contributors', []));
        $newTagsCreated = $this->createNewTags(request()->input('tags-new', []));
        $package->tags()->sync(array_merge(request()->input('tags', []), $newTagsCreated));

        event(new PackageCreated($package));

        if (request('screenshots')) {
            $package->syncScreenshots(request()->input('screenshots', []));
        }

        return redirect()->route('app.packages.index');
    }

    public function edit(Package $package)
    {
        // @todo refactor like store above
        return view('app.packages.edit', [
            'package' => $package,
            'collaborators' => Collaborator::orderBy('name')->get(),
            'tags' => Tag::orderBy('slug')->get(), // @todo maybe group the types first?
            'screenshots' => $package->screenshots,
        ]);
    }

    public function update(PackageFormRequest $request, Package $package)
    {
        $repo = Repo::fromRequest($request);

        $package->update(array_merge(
            request()->only(['name', 'author_id', 'url', 'abstract', 'instructions']),
            [
                'composer_name' => $request->getComposerName(),
                'repo_url' => $repo->url(),
                'readme_source' => $repo->source(),
                'readme_format' => $repo->readmeFormat(),
                'readme' => $repo->readme(),
                'latest_version' => $repo->latestReleaseVersion(),
            ]
        ));

        $package->contributors()->sync($request->input('contributors', []));
        $newTagsCreated = $this->createNewTags($request->input('tags-new', []));
        $package->tags()->sync(array_merge($request->input('tags', []), $newTagsCreated));
        $package->syncScreenshots($request->input('screenshots', []));

        return redirect()->route('app.packages.index');
    }

    // @todo: Race condition where someone else creates the tag in the interim time.
    // Do we firstOrCreate each individually instead?
    private function createNewTags($newTags)
    {
        $created_at = $updated_at = new DateTime;

        $tagNames = collect($newTags)->map(function ($tag) {
            return strtolower($tag);
        });

        $existingTags = Tag::whereIn('name', $tagNames)->get();
        $tagsToCreate = $tagNames->diff($existingTags->pluck('name'));

        if ($tagsToCreate->isEmpty()) {
            return $existingTags->pluck('id')->toArray();
        }

        Tag::insert($tagsToCreate->map(function ($tag) use ($created_at, $updated_at) {
            return [
                'slug' => Str::slug($tag),
                'name' => $tag,
                'created_at' => $created_at,
                'updated_at' => $updated_at,
            ];
        })->toArray());

        return $existingTags
            ->pluck('id')
            ->merge(Tag::whereIn('name', $tagsToCreate)->get()->pluck('id'))
            ->toArray();
    }
}
