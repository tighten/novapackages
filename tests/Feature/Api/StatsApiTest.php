<?php

use App\Models\Collaborator;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;


it('counts live packages', function () {
    fakeNovaReleasesRequest();

    Package::factory(2)->create();
    Package::factory()->disabled()->create();

    $apiCall = $this->get('api/stats')->json();

    expect($apiCall['package_count'])->toEqual(2);
});

it('sums live package download counts', function () {
    fakeNovaReleasesRequest();

    Package::factory()->create(['packagist_downloads' => 123]);
    Package::factory()->create(['packagist_downloads' => 234]);
    Package::factory()->disabled()->create(['packagist_downloads' => 999]);

    $apiCall = $this->get('api/stats')->json();

    expect($apiCall['packagist_download_count'])->toEqual(123 + 234);
});

it('sums live package star counts', function () {
    fakeNovaReleasesRequest();

    Package::factory()->create(['github_stars' => 123]);
    Package::factory()->create(['github_stars' => 234]);
    Package::factory()->disabled()->create(['github_stars' => 999]);

    $apiCall = $this->get('api/stats')->json();

    expect($apiCall['github_star_count'])->toEqual(123 + 234);
});

it('counts collaborators', function () {
    fakeNovaReleasesRequest();

    Collaborator::factory(4)->create();

    $apiCall = $this->get('api/stats')->json();

    expect($apiCall['collaborator_count'])->toEqual(4);
});

it('counts ratings', function () {
    fakeNovaReleasesRequest();

    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $package1 = Package::factory()->create();
    $package2 = Package::factory()->create();
    $package3 = Package::factory()->create();

    $user1->ratePackage($package1, 5);
    $user1->ratePackage($package2, 3);
    $user1->ratePackage($package3, 1);

    $user2->ratePackage($package1, 4);
    $user2->ratePackage($package2, 2);

    $apiCall = $this->get('api/stats')->json();

    expect($apiCall['rating_count'])->toEqual(5);
});

it('averages global rating', function () {
    fakeNovaReleasesRequest();

    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $package1 = Package::factory()->create();
    $package2 = Package::factory()->create();
    $package3 = Package::factory()->create();

    $user1->ratePackage($package1, 5);
    $user1->ratePackage($package2, 3);
    $user1->ratePackage($package3, 1);

    $user2->ratePackage($package1, 4);
    $user2->ratePackage($package2, 2);

    $apiCall = $this->get('api/stats')->json();

    expect($apiCall['average_rating'])->toEqual(3);
});

// Helpers
function fakeNovaReleasesRequest(): void
{
    Http::fake([
        'https://nova.laravel.com/api/releases' => Http::response(
            test()->fakeResponse('nova.laravel.com.api.releases.json')
        ),
    ]);
}
