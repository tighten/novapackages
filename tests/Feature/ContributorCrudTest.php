<?php

use App\Models\Collaborator;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('packages can have contributors', function () {
    $user = User::factory()->create();
    $contributor = Collaborator::factory()->create();
    $user->collaborators()->save($contributor);

    $package = $contributor->contributedPackages()->save(Package::factory()->make());

    expect($package->contributors()->first()->id)->toEqual($contributor->id);
});
