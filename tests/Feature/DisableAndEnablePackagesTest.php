<?php

use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('admin user can disable a package', function () {
    $user = User::factory()->admin()->create();
    $package = Package::factory()->create();

    $response = $this->be($user->fresh())->get(route('app.admin.disable-package', [$package]));
    $response->assertStatus(302);

    $updatedPackage = Package::withoutGlobalScope('notDisabled')->find($package->id);
    $this->assertTrue($updatedPackage->is_disabled);
});

test('admin user can enable a package', function () {
    $user = User::factory()->admin()->create();
    $package = Package::factory()->disabled()->create();

    $response = $this->be($user->fresh())->get(route('app.admin.enable-package', [$package]));
    $response->assertStatus(302);

    $updatedPackage = Package::withoutGlobalScope('notDisabled')->find($package->id);
    $this->assertFalse($updatedPackage->is_disabled);
});
