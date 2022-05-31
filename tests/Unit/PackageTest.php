<?php

namespace Tests\Unit;

use App\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    /**
     * @test
     * @dataProvider packageNameProvider
     */
    public function it_returns_the_display_name_of_the_package($input, $expected)
    {
        $package = Package::make([
            'name' => $input,
        ]);

        $this->assertEquals($expected, $package->display_name);
    }

    /** Data Provider for the package name test. */
    public function packageNameProvider()
    {
        return [
            [
                'input' => 'CKEditor4',
                'expected' => 'CKEditor4',
            ],
            [
                'input' => 'R64 Fields',
                'expected' => 'R64 Fields',
            ],
            [
                'input' => 'ABC Laravel Nova 4',
                'expected' => 'ABC',
            ],
            [
                'input' => 'Laravel Nova 4 ABC',
                'expected' => 'ABC',
            ],
            [
                'input' => 'ABC For Laravel Nova 4',
                'expected' => 'ABC',
            ],
            [
                'input' => 'ABC For Laravel Nova v4',
                'expected' => 'ABC',
            ],
            [
                'input' => 'ABC For n4',
                'expected' => 'ABC',
            ],
            [
                'input' => 'ABC For N 4',
                'expected' => 'ABC',
            ],
            [
                'input' => 'Nova ABC',
                'expected' => 'ABC',
            ],
            [
                'input' => 'Nova 4 ABC',
                'expected' => 'ABC',
            ],
            [
                'input' => 'Nova4 ABC',
                'expected' => 'ABC',
            ],
            [
                'input' => 'ABC for Nova v4',
                'expected' => 'ABC',
            ],
            [
                'input' => 'ABC nova v4',
                'expected' => 'ABC',
            ],
            [
                'input' => 'ABC for v4',
                'expected' => 'ABC',
            ],
            [
                'input' => 'ABC (Nova 4)',
                'expected' => 'ABC',
            ],
            [
                'input' => 'ABC (Nova4)',
                'expected' => 'ABC',
            ],
            [
                'input' => 'ABC v4',
                'expected' => 'ABC',
            ],
            [
                'input' => 'CKEditor3',
                'expected' => 'CKEditor3',
            ],
            [
                'input' => 'R63 Fields',
                'expected' => 'R63 Fields',
            ],
            [
                'input' => 'ABC Laravel Nova 3',
                'expected' => 'ABC',
            ],
            [
                'input' => 'Laravel Nova 3 ABC',
                'expected' => 'ABC',
            ],
            [
                'input' => 'ABC For Laravel Nova 3',
                'expected' => 'ABC',
            ],
            [
                'input' => 'ABC For Laravel Nova v3',
                'expected' => 'ABC',
            ],
            [
                'input' => 'ABC For n3',
                'expected' => 'ABC',
            ],
            [
                'input' => 'ABC For N 3',
                'expected' => 'ABC',
            ],
            [
                'input' => 'Nova ABC',
                'expected' => 'ABC',
            ],
            [
                'input' => 'Nova 3 ABC',
                'expected' => 'ABC',
            ],
            [
                'input' => 'Nova3 ABC',
                'expected' => 'ABC',
            ],
            [
                'input' => 'ABC for Nova v3',
                'expected' => 'ABC',
            ],
            [
                'input' => 'ABC nova v3',
                'expected' => 'ABC',
            ],
            [
                'input' => 'ABC for v3',
                'expected' => 'ABC',
            ],
            [
                'input' => 'ABC (Nova 3)',
                'expected' => 'ABC',
            ],
            [
                'input' => 'ABC (Nova3)',
                'expected' => 'ABC',
            ],
            [
                'input' => 'ABC v3',
                'expected' => 'ABC',
            ],
            [
                'input' => ' ABC ',
                'expected' => 'ABC'
            ],
            [
                'input' => 'ABC For Laravel Nova 4!',
                'expected' => 'ABC !'
            ],
            [
                'input' => 'Nova Oh Dear! Tool',
                'expected' => 'Oh Dear! Tool'
            ],
            [
                'input' => 'Package  with  DoubleSpaces',
                'expected' => 'Package with DoubleSpaces'
            ],
        ];
    }
}
