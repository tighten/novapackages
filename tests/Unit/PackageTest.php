<?php

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

it('returns the abstact when the abstract is set', function () {
    $abstract = 'This is the test abstract';
    $package = Package::factory()->create([
        'abstract' => $abstract,
    ]);

    expect($package->abstract)->toEqual($abstract);
});

it('returns an abstractified readme when the abstract is not set', function () {
    $readme = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';
    $truncatedReadme = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris';

    $package = Package::factory()->create([
        'abstract' => null,
        'readme' => $readme,
    ]);

    expect(strlen(substr($package->abstract, 0, -3)))->toEqual(190);
    expect($package->abstract)->toEqual("{$truncatedReadme}...");
});

it('excludes attributes from being synchronized to the scout search index', function () {
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
});

test('the readme is preserved even when its above 500 characters when being synchronized with the scout index', function () {
    $package = Package::factory()->create([
        'readme' => Str::random(1400),
    ]);

    $searchableArray = $package->toSearchableArray();

    expect(strlen($searchableArray['readme']))->toEqual(1400);
});

test('searchable array preserves chinese characters in readme', function () {
    $chineseReadme = '# nova-amap' . "\n" . 'Laravel Nova高德地图';

    $package = Package::factory()->create([
        'readme' => $chineseReadme,
    ]);

    $searchableArray = $package->toSearchableArray();

    expect($searchableArray['readme'])->toEqual($chineseReadme);
    $this->assertNotFalse(json_encode($searchableArray), 'Searchable array must be JSON-encodable');
});

test('searchable array strips invalid utf8 sequences', function () {
    $invalidUtf8 = "Valid text \xc3\x28 more text";

    $package = Package::factory()->create([
        'readme' => $invalidUtf8,
    ]);

    $searchableArray = $package->toSearchableArray();

    $this->assertNotFalse(json_encode($searchableArray), 'Searchable array must be JSON-encodable');
});

it('returns the display name of the package', function ($input, $expected) {
    $package = Package::make([
        'name' => $input,
    ]);

    expect($package->display_name)->toEqual($expected);
})->with('packageNameProvider');

// Datasets
/** Data Provider for the package name test. */
dataset('packageNameProvider', [
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
        'expected' => 'ABC',
    ],
    [
        'input' => 'ABC For Laravel Nova 4!',
        'expected' => 'ABC !',
    ],
    [
        'input' => 'Nova Oh Dear! Tool',
        'expected' => 'Oh Dear! Tool',
    ],
    [
        'input' => 'Package  with  DoubleSpaces',
        'expected' => 'Package with DoubleSpaces',
    ],
]);
