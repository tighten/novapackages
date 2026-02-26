<?php

namespace Tests\Feature;

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FeedTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_recent_feed_loads(): void
    {
        Package::factory()->create();

        $response = $this->get('feeds/recent');

        $response->assertSuccessful();
    }
}
