<?php

namespace App\Http\Controllers\App;

use App\Events\PackageCreated;
use App\Events\PackageDeleted;
use App\Events\PackageUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\PackageFormRequest;
use App\Models\Collaborator;
use App\Models\Package;
use App\Models\Tag;
use Facades\App\Repo;
use Illuminate\Support\Facades\DB;
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
            'collaborators' => Collaborator::oldest('name')->get(),
            'tags' => Tag::oldest('slug')->get(),
        ]);
    }

    public function store(PackageFormRequest $request)
    {
        $repo = Repo::fromRequest($request);

        // We disable syncing here and manually call ->searchable() after the tag
        // associations are established in the database so they can be indexed.
        $package = Package::withoutSyncingToSearch(function () use ($request, $repo) {
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

            return $package;
        });

        $package->refresh()->searchable();

        event(new PackageCreated($package));

        if (request()->has('screenshots')) {
            $package->syncScreenshots(request()->input('screenshots', []));
        }

        return to_route('app.packages.index');
    }

    public function edit(Package $package)
    {
        // @todo refactor like store above
        return view('app.packages.edit', [
            'package' => $package,
            'collaborators' => Collaborator::oldest('name')->get(),
            'tags' => Tag::oldest('slug')->get(), // @todo maybe group the types first?
            'screenshots' => $package->screenshots,
        ]);
    }

    public function update(PackageFormRequest $request, Package $package)
    {
        $repo = Repo::fromRequest($request);

        // We disable syncing here and manually call ->searchable() after the tag
        // associations are established in the database so they can be indexed.
        $package = Package::withoutSyncingToSearch(function () use ($package, $request, $repo) {
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

            $package->updateAvailabilityFromNewUrl();

            return $package;
        });

        $package->refresh()->searchable();
        event(new PackageUpdated($package));
        $package->syncScreenshots($request->input('screenshots', []));

        return to_route('app.packages.index');
    }

    public function destroy(Package $package)
    {
        $name = $package->name;

        DB::transaction(function () use ($package) {
            $package->contributors()->sync([]);
            $package->tags()->sync([]);
            DB::table('reviews')->where('package_id', $package->id)->delete();
            DB::table('ratings')->where([
                'rateable_id' => $package->id,
                'rateable_type' => $package->getMorphClass(),
            ])->delete();
            DB::table('favorites')->where('package_id', $package->id)->delete();
            $package->screenshots->each->delete();
            $package->delete();
        });

        event(new PackageDeleted($name, auth()->user()));

        session()->flash('status', "{$name} has been deleted.");

        Log::notice("Package {$name} was deleted by user ".auth()->user()->id);

        return to_route('app.packages.index');
    }

    // @todo: Race condition where someone else creates the tag in the interim time.
    // Do we firstOrCreate each individually instead?
    private function createNewTags($newTags)
    {
        $tagNames = collect($newTags)->map(fn ($tag) => strtolower($tag));

        $existingTags = Tag::whereIn('name', $tagNames)->get();
        $tagsToCreate = $tagNames->diff($existingTags->pluck('name'));

        if ($tagsToCreate->isEmpty()) {
            return $existingTags->pluck('id')->toArray();
        }

        Tag::insert($tagsToCreate->map(fn ($tag) => [
            'slug' => Str::slug($tag),
            'name' => $tag,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray());

        return $existingTags
            ->pluck('id')
            ->merge(Tag::whereIn('name', $tagsToCreate)->pluck('id'))
            ->toArray();
    }
}
