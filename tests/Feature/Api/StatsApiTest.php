<?php

namespace Tests\Feature\Api;

use App\Collaborator;
use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatsApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_counts_live_packages()
    {
        Package::factory(2)->create();
        Package::factory()->disabled()->create();

        $apiCall = $this->get('api/stats')->json();

        $this->assertEquals(2, $apiCall['package_count']);
    }

    /** @test */
    public function it_sums_live_package_download_counts()
    {
        Package::factory()->create(['packagist_downloads' => 123]);
        Package::factory()->create(['packagist_downloads' => 234]);
        Package::factory()->disabled()->create(['packagist_downloads' => 999]);

        $apiCall = $this->get('api/stats')->json();

        $this->assertEquals(123 + 234, $apiCall['packagist_download_count']);
    }

    /** @test */
    public function it_sums_live_package_star_counts()
    {
        Package::factory()->create(['github_stars' => 123]);
        Package::factory()->create(['github_stars' => 234]);
        Package::factory()->disabled()->create(['github_stars' => 999]);

        $apiCall = $this->get('api/stats')->json();

        $this->assertEquals(123 + 234, $apiCall['github_star_count']);
    }

    /** @test */
    public function it_counts_collaborators()
    {
        Collaborator::factory(4)->create();

        $apiCall = $this->get('api/stats')->json();

        $this->assertEquals(4, $apiCall['collaborator_count']);
    }

    /** @test */
    public function it_counts_ratings()
    {
        $this->markTestIncomplete('Waiting on latest PR to merge');
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $package1 = User::factory()->create();
        $package2 = User::factory()->create();
        $package3 = User::factory()->create();

        $user1->ratePackage($package1, 5);
        $user1->ratePackage($package2, 3);
        $user1->ratePackage($package3, 1);

        $user2->ratePackage($package1, 4);
        $user2->ratePackage($package2, 2);

        $apiCall = $this->get('api/stats')->json();

        $this->assertEquals(5, $apiCall['rating_count']);
    }

    /** @test */
    public function it_averages_global_rating()
    {
        $this->markTestIncomplete('Waiting on latest PR to merge');
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $package1 = User::factory()->create();
        $package2 = User::factory()->create();
        $package3 = User::factory()->create();

        $user1->ratePackage($package1, 5);
        $user1->ratePackage($package2, 3);
        $user1->ratePackage($package3, 1);

        $user2->ratePackage($package1, 4);
        $user2->ratePackage($package2, 2);

        $apiCall = $this->get('api/stats')->json();

        $this->assertEquals(3, $apiCall['rating_count']);
    }
}
