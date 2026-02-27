<?php

use App\Http\Resources\PackageResource;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('the abstract is returned if the resource has an abstract', function () {
    $abstract = 'This is the test abstract';
    $package = Package::factory()->create([
        'abstract' => $abstract,
        'description' => 'This is the test description',
    ]);

    $packageResource = (PackageResource::from($package));

    expect($packageResource['abstract'])->toEqual($abstract);
});

test('an abstractified value is returned when the abstract is null', function () {
    $package = Package::factory()->create([
        'abstract' => null,
    ]);

    $packageResource = (PackageResource::from($package));

    $this->assertNotNull($packageResource['abstract']);
    expect($package->abstract)->toEqual($packageResource['abstract']);
});
