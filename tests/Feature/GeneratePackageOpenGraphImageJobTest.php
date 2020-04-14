<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Jobs\GeneratePackageOpenGraphImage;
use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** @runTestsInSeparateProcesses */
class GeneratePackageOpenGraphImageJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_is_dispatched_when_a_package_is_viewed()
    {
        Bus::fake();

        $packageNamespace = 'abcs';
        $packageName = 'lmnop';

        $package = factory(Package::class)->make([
            'composer_name' => "{$packageNamespace}/{$packageName}",
        ]);

        $collaborator = factory(Collaborator::class)->make();
        $user = factory(User::class)->create();
        $user->collaborators()->save($collaborator);
        $collaborator->authoredPackages()->save($package);

        $response = $this->actingAs($user)->get(route('packages.show', [
            'namespace' => $packageNamespace,
            'name' => $packageName,
        ]));

        $response->assertSuccessful();

        Bus::assertDispatched(GeneratePackageOpenGraphImage::class);
    }

    /** @test */
    function it_creates_a_new_image_and_saves_to_storage()
    {
        GeneratePackageOpenGraphImage::dispatch('Alphabets', 'Sesame Street');

        $file = 'og/' . str_slug('Alphabets') . '.png';

        Storage::disk('public')->assertExists($file);

        Storage::delete($file);

        Storage::disk('public')->assertMissing($file);
    }
}
