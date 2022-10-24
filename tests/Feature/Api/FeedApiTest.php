<?php

namespace Tests\Feature\Api;

use App\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedApiTest extends TestCase
{
    use RefreshDatabase;

    public function it_counts_packages()
    {
        Package::factory(5)->create();

        $response = $this->getJson('/api/packages.json');

        $response
            ->assertStatus(200)
            ->assertJsonCount(5);
    }
}
