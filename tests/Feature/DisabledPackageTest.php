<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DisabledPackageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function packages_show_on_main_page(): void
    {
        $user = User::factory()->admin()->create();
        $package = Package::factory()->create();

        $response = $this->be($user)->get(route('home'));
        $response->assertSee($package->name);
    }

    #[Test]
    public function disabled_packages_dont_show_on_main_page(): void
    {
        $user = User::factory()->admin()->create();
        $package = Package::factory()->disabled()->create();

        $response = $this->be($user)->get(route('home'));
        $response->assertDontSee($package->name);
    }

    #[Test]
    public function disabled_packages_dont_return_from_api(): void
    {
        $this->markTestIncomplete();
    }
}
