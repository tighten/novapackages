<?php

namespace Tests\Unit;

use App\Http\Resources\PackageDetailResource;
use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class PackageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_the_abstact_when_the_abstract_is_set()
    {
        $abstract = 'This is the test abstract';
        $package = Package::factory()->create([
            'abstract' => $abstract,
        ]);

        $this->assertEquals($abstract, $package->abstract);
    }

    /** @test */
    public function it_returns_an_abstractified_readme_when_the_abstract_is_not_set()
    {
        $readme = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';
        $truncatedReadme = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris';

        $package = Package::factory()->create([
            'abstract' => null,
            'readme' => $readme,
        ]);

        $this->assertEquals(190, strlen(substr($package->abstract, 0, -3)));
        $this->assertEquals("{$truncatedReadme}...", $package->abstract);
    }

    /** @test */
    public function it_excludes_attributes_from_being_synchronized_to_the_scout_search_index()
    {
        $notSearchableAttributes = [
            'description',
            'packagist_downloads',
            'github_stars',
            'updated_at',
        ];

        $package = Package::factory()->create([
            'description' => 'Test description',
            'packagist_downloads' => 1,
            'github_stars' => 1,
            'updated_at' => Carbon::now(),
        ]);

        $searchableArray = $package->toSearchableArray();

        $this->assertArrayNotHasKey($notSearchableAttributes[0], $searchableArray);
        $this->assertArrayNotHasKey($notSearchableAttributes[1], $searchableArray);
        $this->assertArrayNotHasKey($notSearchableAttributes[2], $searchableArray);
        $this->assertArrayNotHasKey($notSearchableAttributes[3], $searchableArray);
    }

    /** @test */
    public function the_readme_is_truncated_to_500_characters_when_being_synchronized_with_the_scout_index()
    {
        $package = Package::factory()->create([
            'readme' => Str::random(1400),
        ]);

        $searchableArray = $package->toSearchableArray();

        $this->assertEquals(500, strlen($searchableArray['readme']));
    }
}
