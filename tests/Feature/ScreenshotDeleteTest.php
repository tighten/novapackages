<?php

use App\Models\Collaborator;
use App\Models\Package;
use App\Models\Screenshot;
use App\Models\User;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;

test('a screenshot can be deleted by the person who uploaded it', function () {
    Storage::fake();

    $user = User::factory()->create();
    $screenshotA = Screenshot::factory()->create([
        'uploader_id' => $user->id,
        'path' => File::create('screenshot.jpg')->store('screenshots'),
    ]);
    $screenshotB = Screenshot::factory()->create([
        'uploader_id' => $user->id,
        'path' => File::create('screenshot.jpg')->store('screenshots'),
    ]);

    $response = $this->actingAs($user)->json('DELETE', route('app.screenshot-uploads.destroy', $screenshotA), [
        'X-Requested-With' => 'XMLHttpRequest',
    ]);

    $response->assertSuccessful();
    $screenshots = Screenshot::all();
    expect($screenshots)->toHaveCount(1);
    expect($screenshots->contains($screenshotB))->toBeTrue();
    expect($screenshots->contains($screenshotA))->toBeFalse();
    Storage::assertMissing($screenshotA->path);
});

test('a package collaborator can delete an attached screenshot', function () {
    Storage::fake();

    [$package, $collaboratorUser] = createPackageWithUser();
    $uploader = User::factory()->create();
    $packageScreenshot = Screenshot::factory()->create([
        'uploader_id' => $uploader->id,
        'path' => File::create('screenshotA.jpg')->store('screenshots'),
    ]);
    $package->screenshots()->save($packageScreenshot);

    $response = $this->actingAs($collaboratorUser)->json('DELETE', route('app.screenshot-uploads.destroy', $packageScreenshot), [
        'X-Requested-With' => 'XMLHttpRequest',
    ]);

    $response->assertSuccessful();
    expect(Screenshot::all())->toHaveCount(0);
    Storage::assertMissing($packageScreenshot->path);
});

test('an unauthorized user can not delete a screenshot', function () {
    Storage::fake();

    $user = User::factory()->create();
    $screenshot = Screenshot::factory()->create([
        'path' => File::create('screenshot.jpg')->store('screenshots'),
    ]);

    $response = $this->actingAs($user)->json('DELETE', route('app.screenshot-uploads.destroy', $screenshot), [
        'X-Requested-With' => 'XMLHttpRequest',
    ]);

    $response->assertStatus(403);
    $screenshots = Screenshot::all();
    expect($screenshots)->toHaveCount(1);
    expect($screenshots->contains($screenshot))->toBeTrue();
    Storage::assertExists($screenshot->path);
});

test('a guest user can not delete a screenshot', function () {
    Storage::fake();

    $screenshot = Screenshot::factory()->create([
        'path' => File::create('screenshotA.jpg')->store('screenshots'),
    ]);

    $response = $this->json('DELETE', route('app.screenshot-uploads.destroy', $screenshot), [
        'X-Requested-With' => 'XMLHttpRequest',
    ]);

    $response->assertStatus(401);
    expect(Screenshot::all())->toHaveCount(1);
    expect(Screenshot::first()->is($screenshot))->toBeTrue();
    Storage::assertExists($screenshot->path);
});

// Helpers
function createPackageWithUser()
{
    $package = Package::factory()->make();
    $collaborator = Collaborator::factory()->make();
    $user = User::factory()->create();
    $user->collaborators()->save($collaborator);
    $collaborator->authoredPackages()->save($package);

    return [$package, $user];
}
