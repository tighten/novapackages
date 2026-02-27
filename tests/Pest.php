<?php

uses(\Tests\TestCase::class)->in('Feature', 'Integration');
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

/** @link https://pestphp.com/docs/configuring-tests */

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

/** @link https://pestphp.com/docs/custom-expectations */

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/** @link https://pestphp.com/docs/custom-helpers */

use App\Models\Collaborator;
use App\Models\Package;
use App\Models\Tag;
use App\Models\User;

function postFromPackage($package)
{
    $packagistInformation = explode('/', $package->composer_name);

    return [
        'author_id' => $package->author_id,
        'name' => $package->name,
        'packagist_namespace' => $packagistInformation[0],
        'packagist_name' => $packagistInformation[1],
        'url' => $package->url,
        'description' => $package->description,
        'abstract' => $package->abstract,
        'instructions' => $package->instructions,
    ];
}

function createPackageWithUser()
{
    $package = Package::factory()->make();
    $collaborator = Collaborator::factory()->make();
    $user = User::factory()->create();
    $user->collaborators()->save($collaborator);
    $collaborator->authoredPackages()->save($package);
    $package->tags()->save(Tag::factory()->create());

    return [$package, $user];
}
