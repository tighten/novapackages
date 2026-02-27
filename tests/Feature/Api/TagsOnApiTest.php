<?php

use App\Models\Package;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

it('attaches tags to api responses', function () {
    $package = Package::factory()->create();
    $tag = Tag::factory()->create();
    $package->tags()->attach($tag);

    $apiCall = $this->get('api/recent')->json();

    $tags = reset($apiCall['data'])['tags'];
    $this->assertCount(1, $tags);
    $this->assertEquals($tag->slug, reset($tags)['slug']);
    $this->assertEquals($tag->name, reset($tags)['name']);
});
