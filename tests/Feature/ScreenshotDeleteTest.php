<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Package;
use App\Screenshot;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ScreenshotDeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_screenshot_can_be_deleted_by_the_person_who_uploaded_it()
    {
        Storage::fake();

        $user = factory(User::class)->create();
        $screenshotA = factory(Screenshot::class)->create([
            'uploader_id' => $user->id,
            'path' => File::create('screenshot.jpg')->store('screenshots'),
        ]);
        $screenshotB = factory(Screenshot::class)->create([
            'uploader_id' => $user->id,
            'path' => File::create('screenshot.jpg')->store('screenshots'),
        ]);

        $response = $this->actingAs($user)->json('DELETE', route('app.screenshot-uploads.destroy', $screenshotA), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertSuccessful();
        $screenshots = Screenshot::all();
        $this->assertCount(1, $screenshots);
        $this->assertTrue($screenshots->contains($screenshotB));
        $this->assertFalse($screenshots->contains($screenshotA));
        Storage::assertMissing($screenshotA->path);
    }

    /** @test */
    public function a_package_collaborator_can_delete_an_attached_screenshot()
    {
        Storage::fake();

        list($package, $collaboratorUser) = $this->createPackageWithUser();
        $uploader = factory(User::class)->create();
        $packageScreenshot = factory(Screenshot::class)->create([
            'uploader_id' => $uploader->id,
            'path' => File::create('screenshotA.jpg')->store('screenshots'),
        ]);
        $package->screenshots()->save($packageScreenshot);

        $response = $this->actingAs($collaboratorUser)->json('DELETE', route('app.screenshot-uploads.destroy', $packageScreenshot), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertSuccessful();
        $this->assertCount(0, Screenshot::all());
        Storage::assertMissing($packageScreenshot->path);
    }

    /** @test */
    public function an_unauthorized_user_can_not_delete_a_screenshot()
    {
        Storage::fake();

        $user = factory(User::class)->create();
        $screenshot = factory(Screenshot::class)->create([
            'path' => File::create('screenshot.jpg')->store('screenshots'),
        ]);

        $response = $this->actingAs($user)->json('DELETE', route('app.screenshot-uploads.destroy', $screenshot), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(403);
        $screenshots = Screenshot::all();
        $this->assertCount(1, $screenshots);
        $this->assertTrue($screenshots->contains($screenshot));
        Storage::assertExists($screenshot->path);
    }

    /** @test */
    public function a_guest_user_can_not_delete_a_screenshot()
    {
        Storage::fake();

        $screenshot = factory(Screenshot::class)->create([
            'path' => File::create('screenshotA.jpg')->store('screenshots'),
        ]);

        $response = $this->json('DELETE', route('app.screenshot-uploads.destroy', $screenshot), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(401);
        $this->assertCount(1, Screenshot::all());
        $this->assertTrue(Screenshot::first()->is($screenshot));
        Storage::assertExists($screenshot->path);
    }

    private function createPackageWithUser()
    {
        $package = factory(Package::class)->make();
        $collaborator = factory(Collaborator::class)->make();
        $user = factory(User::class)->create();
        $user->collaborators()->save($collaborator);
        $collaborator->authoredPackages()->save($package);

        return [$package, $user];
    }
}
