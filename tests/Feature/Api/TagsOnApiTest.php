<?php

use App\Models\Package;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


it('attaches tags to api responses', function () {
    $package = Package::factory()->create();
    $tag = Tag::factory()->create();
    $package->tags()->attach($tag);

    $apiCall = $this->get('api/recent')->json();

    $tags = reset($apiCall['data'])['tags'];
    expect($tags)->toHaveCount(1);
    expect(reset($tags)['slug'])->toEqual($tag->slug);
    expect(reset($tags)['name'])->toEqual($tag->name);
});
