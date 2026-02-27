<?php

use App\Events\PackageCreated;
use App\Events\PackageUpdated;
use App\Jobs\GeneratePackageOpenGraphImage;
use App\Listeners\PackageEventSubscriber;
use App\Models\Collaborator;
use App\Models\Package;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;


it('is dispatched when a package is created', function () {
    Bus::fake();

    Event::fake();

    $collaborator = Collaborator::factory()->make();
    $user = User::factory()->create();
    $user->collaborators()->save($collaborator);
    $package = Package::factory()->make([
        'author_id' => $user->collaborators->first()->id,
        'url' => 'https://www.example.com/abcs/lmnop',
    ]);

    $formData = array_merge(postFromPackage($package), [
        'tags-new' => ['New tag'],
        'tags' => [Tag::factory()->create()->id],
    ]);

    $this->fakesRepoFromRequest([
        'url' => $formData['url'],
        'source' => 'fake-repo-source',
        'readme' => '# Fake Repo Readme',
        'latest_version' => 'v1.2.3',
    ]);

    $this->actingAs($user)
        ->followingRedirects()
        ->post(route('app.packages.store', $formData))
        ->assertSuccessful();

    $package = Package::whereUrl('https://www.example.com/abcs/lmnop')->first();

    Event::assertDispatched(PackageCreated::class, function ($event) use ($package) {
        return $event->package->id === $package->id;
    });

    (new PackageEventSubscriber)->handle(new PackageCreated($package));

    Bus::assertDispatched(GeneratePackageOpenGraphImage::class);
});

it('is dispatched when a package is updated', function () {
    Bus::fake();

    Event::fake();

    $package = Package::factory()->make();
    $collaborator = Collaborator::factory()->make();
    $user = User::factory()->create();
    $user->collaborators()->save($collaborator);
    $collaborator->authoredPackages()->save($package);

    $formData = array_merge(postFromPackage($package), [
        'tags-new' => ['New tag'],
        'tags' => [Tag::factory()->create()->id],
    ]);

    $this->fakesRepoFromRequest([
        'url' => $formData['url'],
        'source' => 'fake-repo-source',
        'readme' => '# Fake Repo Readme',
        'latest_version' => 'v1.2.3',
    ]);

    $this->actingAs($user)->put(route('app.packages.update', $package), $formData);

    Event::assertDispatched(PackageUpdated::class);

    (new PackageEventSubscriber)->handle(new PackageUpdated($package));

    Bus::assertDispatched(GeneratePackageOpenGraphImage::class);
});

it('creates a new image and saves to storage', function () {
    $packageName = 'Alphabets';

    $filePath = createANewOpenGraphImage($packageName);

    Storage::disk('public')->assertExists($filePath);

    Storage::delete($filePath);

    Storage::disk('public')->assertMissing($filePath);
});

it('removes the old image from storage when a package name is updated', function () {
    $originalPackageName = 'Alphabets';
    $updatedPackageName = 'Alphabets And Numbers';

    $filePath = createANewOpenGraphImage($originalPackageName);

    Storage::disk('public')->assertExists($filePath);

    $newFilePath = createANewOpenGraphImage($updatedPackageName);

    Storage::disk('public')->assertExists($newFilePath);

    Storage::disk('public')->assertMissing($filePath);

    // clean up
    Storage::delete($newFilePath);

    Storage::disk('public')->assertMissing($newFilePath);
});

// Helpers
function createANewOpenGraphImage($packageName)
{
    $file = '123_' . Str::slug($packageName) . '.png';
    $filePath = config('opengraph.image_directory_name') . "/{$file}";

    GeneratePackageOpenGraphImage::dispatch($packageName, 'Sesame Street', $file);

    return $filePath;
}

function postFromPackage($package)
{
    $packagistInformation = explode('/', $package->composer_name);

    return [
        'author_id' => $package->author_id,
        'name' => $package->name,
        'packagist_namespace' => $packagistInformation[0],
        'packagist_name' => $packagistInformation[1],
        'url' => $package->url,
        'description' => $package->description,
        'abstract' => $package->abstract,
        'instructions' => $package->instructions,
    ];
}
