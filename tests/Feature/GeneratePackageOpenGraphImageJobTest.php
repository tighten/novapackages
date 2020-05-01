<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Events\PackageCreated;
use App\Events\PackageUpdated;
use App\Jobs\GeneratePackageOpenGraphImage;
use App\Listeners\PackageEventSubscriber;
use App\Package;
use App\Tag;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class GeneratePackageOpenGraphImageJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_is_dispatched_when_a_package_is_created()
    {
        Bus::fake();

        Event::fake();

        $collaborator = factory(Collaborator::class)->make();
        $user = factory(User::class)->create();
        $user->collaborators()->save($collaborator);
        $package = factory(Package::class)->make([
            'author_id' => $user->collaborators->first()->id,
            'url' => 'https://www.example.com/abcs/lmnop',
        ]);

        $formData = array_merge($this->postFromPackage($package), [
            'tags-new' => ['New tag'],
            'tags' => [factory(Tag::class)->create()->id],
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

        Event::assertDispatched(PackageCreated::class);

        (new PackageEventSubscriber)->handle(new PackageCreated($package));

        Bus::assertDispatched(GeneratePackageOpenGraphImage::class);
    }

    /** @test */
    function it_is_dispatched_when_a_package_is_updated()
    {
        Bus::fake();

        Event::fake();

        $package = factory(Package::class)->make();
        $collaborator = factory(Collaborator::class)->make();
        $user = factory(User::class)->create();
        $user->collaborators()->save($collaborator);
        $collaborator->authoredPackages()->save($package);

        $formData = array_merge($this->postFromPackage($package), [
            'tags-new' => ['New tag'],
            'tags' => [factory(Tag::class)->create()->id],
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
    }

    /** @test */
    function it_creates_a_new_image_and_saves_to_storage()
    {
        $packageName = 'Alphabets';

        $filePath = $this->createANewOpenGraphImage($packageName);

        Storage::disk('public')->assertExists($filePath);

        Storage::delete($filePath);

        Storage::disk('public')->assertMissing($filePath);
    }

    /** @test */
    function it_removes_the_old_image_from_storage_when_a_package_name_is_updated()
    {
        $originalPackageName = 'Alphabets';
        $updatedPackageName = 'Alphabets And Numbers';

        $filePath = $this->createANewOpenGraphImage($originalPackageName);

        Storage::disk('public')->assertExists($filePath);

        $newFilePath = $this->createANewOpenGraphImage($updatedPackageName);

        Storage::disk('public')->assertExists($newFilePath);

        Storage::disk('public')->assertMissing($filePath);

        // clean up
        Storage::delete($newFilePath);

        Storage::disk('public')->assertMissing($newFilePath);
    }

    function createANewOpenGraphImage($packageName)
    {
        $file = '123_' . Str::slug($packageName) . '.png';
        $filePath = 'ogimage/' . $file;

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
}
