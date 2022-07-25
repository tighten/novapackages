<?php

namespace Tests\Feature\Api;

use App\Package;
use App\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PopularPackageApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function returns_popular_packages_in_order()
    {
        $lessPopularPackage = Package::factory()->create([
            'github_stars' => 10,
            'packagist_downloads' => 1000,
        ]);

        $morePopularPackage = Package::factory()->create([
            'github_stars' => 20,
            'packagist_downloads' => 2000,
        ]);

        $tag = Tag::factory()->create();

        $morePopularPackage->tags()->attach($tag);

        $response = $this->get(route('api.popular-packages'));

        $this->assertEquals($morePopularPackage->name, $response->json('data')[0]['name']);
        $this->assertEquals($lessPopularPackage->name, $response->json('data')[1]['name']);

        $this->assertEquals($morePopularPackage->author->name, $response->json('data')[0]['author']['name']);

        $this->assertEquals($tag->name, $response->json('data')[0]['tags'][0]['name']);
    }
}
