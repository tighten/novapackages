<?php

namespace Tests\Feature\Api;

use App\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class FeedApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ensures_packages_feed_response_code_and_structure()
    {
        Package::factory(5)->create();

        $response = $this->getJson('/api/packages.json');

        $response
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json
                    ->count(5)
                    ->first(function (AssertableJson $json) {
                        $json->hasAll(['name', 'author', 'abstract', 'url', 'tags']);
                    });
            });
    }
}
