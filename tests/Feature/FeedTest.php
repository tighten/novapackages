<?php

namespace Tests\Feature;

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_recent_feed_loads(): void
    {
        Package::factory()->create();

        $response = $this->get('feeds/recent');

        $response->assertSuccessful();
    }
}
