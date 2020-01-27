<?php

namespace Tests\Feature\Api;

use App\Package;
use App\Tag;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TagsOnApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_attaches_tags_to_api_responses()
    {
        $package = factory(Package::class)->create();
        $tag = factory(Tag::class)->create();
        $package->tags()->attach($tag);

        $apiCall = $this->get('api/recent')->json();

        $tags = reset($apiCall['data'])['tags'];
        $this->assertCount(1, $tags);
        $this->assertEquals($tag->slug, reset($tags)['slug']);
        $this->assertEquals($tag->name, reset($tags)['name']);
    }
}
