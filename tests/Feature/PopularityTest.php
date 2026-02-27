<?php

use App\Models\Collaborator;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('github stars influence popularity', function () {
    $user = User::factory()->create();
    $collaborator = Collaborator::factory()->create();
    $user->collaborators()->save($collaborator);

    $collaborator->authoredPackages()->saveMany(Package::factory(20)->make());

    $popularPackages = Package::inRandomOrder()->take(10)->get();

    $popularPackages->each(function ($package) {
        $package->update(['github_stars' => 25]);
    });

    $popularScope = Package::popular()->take(10)->pluck('id')->toArray();
    expect(array_intersect($popularScope, $popularPackages->pluck('id')->toArray()))->toHaveCount(10);
});

test('packagist downloads influence popularity', function () {
    $user = User::factory()->create();
    $collaborator = Collaborator::factory()->create();
    $user->collaborators()->save($collaborator);

    $collaborator->authoredPackages()->saveMany(Package::factory(20)->make());

    $popularPackages = Package::inRandomOrder()->take(10)->get();

    $popularPackages->each(function ($package) {
        $package->update(['packagist_downloads' => 25]);
    });

    $popularScope = Package::popular()->take(10)->pluck('id')->toArray();
    expect(array_intersect($popularScope, $popularPackages->pluck('id')->toArray()))->toHaveCount(10);
});
