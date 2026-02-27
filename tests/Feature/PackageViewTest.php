<?php

use App\Models\Collaborator;
use App\Models\Package;
use App\Models\Screenshot;
use App\Models\User;
use Illuminate\Support\Facades\Http;

test('a user can view the show package page', function () {
    $packageNamespace = 'tightenco';
    $packageName = 'bae';
    $packageA = Package::factory()->make([
        'composer_name' => "{$packageNamespace}/{$packageName}",
    ]);
    $collaborator = Collaborator::factory()->make();
    $user = User::factory()->create();
    $user->collaborators()->save($collaborator);
    $collaborator->authoredPackages()->save($packageA);
    $screenshot = Screenshot::factory()->create(['uploader_id' => $user->id]);
    $packageA->screenshots()->save($screenshot);
    $packageB = Package::factory()->create();

    Http::fake([
        "https://packagist.org/packages/{$packageA->composer_name}.json" => Http::response(),
    ]);

    $response = $this->actingAs($user)
        ->get(route('packages.show', ['namespace' => $packageNamespace, 'name' => $packageName]));

    $response->assertSuccessful();
    $response->assertViewHas('package');
});

test('legacy package id lookup redirects to namespace search', function () {
    $packageNamespace = 'tightenco';
    $packageName = 'bae';
    $package = Package::factory()->make([
        'composer_name' => "{$packageNamespace}/{$packageName}",
    ]);
    $collaborator = Collaborator::factory()->make();
    $user = User::factory()->create();
    $user->collaborators()->save($collaborator);
    $collaborator->authoredPackages()->save($package);

    $response = $this->actingAs($user)
        ->get(route('packages.show-id', ['package' => $package->id]));

    $response->assertRedirect(route('packages.show', ['namespace' => $packageNamespace, 'name' => $packageName]));
});
