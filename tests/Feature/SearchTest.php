<?php

namespace Tests\Feature;

use App\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->markTestIncomplete('Need to figure out how to test with Scout.');
    }

    /** @test */
    public function it_returns_matching_results()
    {
        $package = Package::factory()->create(['name' => 'Dancing hyenas']);

        $response = $this->get(route('search', ['q' => 'hyenas']));

        $response->assertSee('Dancing hyenas');
    }

    /** @test */
    public function it_doesnt_return_non_matching_results()
    {
        $package = Package::factory()->create(['name' => 'Dancing hyenas']);

        $response = $this->get(route('search', ['q' => 'acrobats']));

        $response->assertDontSee('Dancing hyenas');
    }

    /** @test */
    public function it_ignores_disabled_packages()
    {
        $package2 = Package::factory()->disabled()->create(['name' => 'An alligator']);

        $response = $this->get(route('search', ['q' => 'a']));

        $response->assertDontSee('alligator');
    }
}
