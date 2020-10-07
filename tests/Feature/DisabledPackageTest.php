<?php

namespace Tests\Feature;

use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisabledPackageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function packages_show_on_main_page()
    {
        $user = factory(User::class)->state('admin')->create();
        $package = factory(Package::class)->create();

        $response = $this->be($user)->get(route('home'));
        $response->assertSee($package->name);
    }

    /** @test */
    public function disabled_packages_dont_show_on_main_page()
    {
        $user = factory(User::class)->state('admin')->create();
        $package = factory(Package::class)->states('disabled')->create();

        $response = $this->be($user)->get(route('home'));
        $response->assertDontSee($package->name);
    }

    /** @test */
    public function disabled_packages_dont_return_from_api()
    {
        $this->markTestIncomplete();
    }
}
