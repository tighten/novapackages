<?php

namespace Tests\Feature\Api;

use App\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedApiTest extends TestCase
{
    use RefreshDatabase;

    public function testBasic()
    {
        Package::factory(5)->create();

        $response = $this->get('/api/packages.json')->json();

        $response
            ->assertStatus(200)
            ->assertJsonCount(5);
    }
}
