<?php

namespace Tests\Unit;

use App\Models\Package;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

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
    }

    /** @test */
    public function it_can_be_scoped_by_number_of_associated_packages()
    {
        $tagWithMostPackages = Tag::nonTypes()
            ->whereHas('packages')
            ->withCount('packages')
            ->latest('packages_count')
            ->first();
        $popularTags = Tag::popular()->take(3)->get();

        $this->assertEquals($tagWithMostPackages, $popularTags->first());
        $this->assertEmpty($popularTags->pluck('slug')->intersect(Tag::PROJECT_TYPES));
        $this->assertLessThanOrEqual($popularTags->first()->packages->count(), $popularTags->last()->packages->count());
    }

    /** @test */
    public function it_can_be_scoped_by_only_project_types()
    {
        $tags = Tag::types()->get();

        $this->assertNotEmpty($tags->pluck('slug')->intersect(Tag::PROJECT_TYPES));
        $this->assertEquals($tags->pluck('slug')->count(), count(Tag::PROJECT_TYPES));
        $this->assertEmpty($tags->pluck('slug')->intersect(Tag::nonTypes()->pluck('slug')));
    }

    /** @test */
    public function it_can_be_scoped_to_exclude_project_types()
    {
        $this->assertEmpty(Tag::nonTypes()->pluck('slug')->intersect(Tag::PROJECT_TYPES));
    }

    /** @test */
    public function the_name_attribute_is_stored_as_lowercase()
    {
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
    }

    /** @test */
    public function it_can_generate_its_own_url()
    {
        $tag = Tag::factory()->create(['slug' => 'abc']);

        $this->assertEquals(url('?tag=abc'), $tag->url());
    }
}
