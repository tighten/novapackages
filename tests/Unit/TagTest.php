<?php

use App\Models\Package;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);
beforeEach(function () {
    foreach (Tag::PROJECT_TYPES as $name) {
        Tag::create(['name' => $name, 'slug' => Str::slug($name)]);
    }

    $tags = Tag::factory(5)->create();
    $packages = Package::factory(20)->create();

    $tags->each(function ($tag) use ($packages) {
        $packages->shuffle()->take(rand(10, 15))->each(function ($package) use ($tag) {
            $package->tags()->attach($tag);
        });
    });
});


it('can be scoped by number of associated packages', function () {
    $tagWithMostPackages = Tag::nonTypes()
        ->whereHas('packages')
        ->withCount('packages')
        ->orderByDesc('packages_count')
        ->first();
    $popularTags = Tag::popular()->take(3)->get();

    $this->assertEquals($tagWithMostPackages, $popularTags->first());
    $this->assertEmpty($popularTags->pluck('slug')->intersect(Tag::PROJECT_TYPES));
    $this->assertLessThanOrEqual($popularTags->first()->packages->count(), $popularTags->last()->packages->count());
});

it('can be scoped by only project types', function () {
    $tags = Tag::types()->get();

    $this->assertNotEmpty($tags->pluck('slug')->intersect(Tag::PROJECT_TYPES));
    $this->assertEquals($tags->pluck('slug')->count(), count(Tag::PROJECT_TYPES));
    $this->assertEmpty($tags->pluck('slug')->intersect(Tag::nonTypes()->pluck('slug')));
});

it('can be scoped to exclude project types', function () {
    $this->assertEmpty(Tag::nonTypes()->pluck('slug')->intersect(Tag::PROJECT_TYPES));
});

test('the name attribute is stored as lowercase', function () {
    $name = 'Test name';
    $tag = Tag::factory()->make([
        'name' => $name,
    ]);

    Tag::create($tag->toArray());

    // Note: sqlite is case sensitive by default and mysql is generally not. We find
    // the tag by converting the test name to lower case to avoid a sqlite issue
    // and use strcmp to make a case sensitive comparison for mysql.
    $tag = Tag::where('name', Str::lower($name))->first();
    $this->assertTrue(strcmp(Str::lower($name), $tag->name) === 0);
});

it('can generate its own url', function () {
    $tag = Tag::factory()->create(['slug' => 'abc']);

    $this->assertEquals(url('?tag=abc'), $tag->url());
});
