<?php

namespace Tests\Feature;

use App\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteMapTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function displays_sitemap()
    {
        [$packageA, $packageB] = Package::factory()->count(2)->create();

        $response = $this->get(route('sitemap'));

        $response->assertSuccessful();

        $response->assertSeeText($packageA->composer_name);
        $response->assertSeeText($packageB->composer_name);
    }
}
