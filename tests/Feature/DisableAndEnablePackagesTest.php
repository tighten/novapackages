<?php

namespace Tests\Feature;

use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisableAndEnablePackagesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_user_can_disable_a_package()
    {
        $user = factory(User::class, 'admin')->create();
        $package = factory(Package::class)->create();

        $response = $this->be($user->fresh())->get(route('app.admin.disable-package', [$package]));
        $response->assertStatus(302);

        $updatedPackage = Package::withoutGlobalScope('notDisabled')->find($package->id);
        $this->assertTrue($updatedPackage->is_disabled);
    }

    /** @test */
    public function admin_user_can_enable_a_package()
    {
        $user = factory(User::class, 'admin')->create();
        $package = factory(Package::class)->state('disabled')->create();

        $response = $this->be($user->fresh())->get(route('app.admin.enable-package', [$package]));
        $response->assertStatus(302);

        $updatedPackage = Package::withoutGlobalScope('notDisabled')->find($package->id);
        $this->assertFalse($updatedPackage->is_disabled);
    }
}
