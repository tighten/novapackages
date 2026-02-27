<?php

use App\Models\Collaborator;
use App\Models\Package;
use App\Models\Screenshot;
use App\Models\User;
use Illuminate\Support\Facades\Http;

test('a packages screenshots are passed to the view', function () {
    Http::fake(['https://packagist.org/packages/tightenco/bae.json' => Http::response()]);

    $packageNamespace = 'tightenco';
    $packageName = 'bae';
    $user = User::factory()->create();
    $collaborator = Collaborator::factory()->make();
    $user->collaborators()->save($collaborator);
    $packageA = Package::factory()->make([
        'composer_name' => "{$packageNamespace}/{$packageName}",
    ]);
    $collaborator->authoredPackages()->save($packageA);
    $screenshotA = Screenshot::factory()->create(['uploader_id' => $user->id]);
    $packageA->screenshots()->save($screenshotA);
    $packageB = Package::factory()->create();
    $screenshotB = Screenshot::factory()->create(['uploader_id' => $user->id]);
    $packageB->screenshots()->save($screenshotB);

    $response = $this->actingAs($user)
        ->get(route('packages.show', ['namespace' => $packageNamespace, 'name' => $packageName]));
    $response->assertSuccessful();

    // Screenshots are now loaded within the Livewire component, not passed as view data
    $package = $response->viewData('package');
    expect($package->screenshots)->toHaveCount(1);
    expect($package->screenshots->contains($screenshotA))->toBeTrue();
    expect($package->screenshots->contains($screenshotB))->toBeFalse();
});
