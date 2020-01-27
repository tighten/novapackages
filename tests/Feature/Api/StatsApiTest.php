<?php

namespace Tests\Feature\Api;

use App\Collaborator;
use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StatsApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_counts_live_packages()
    {
        factory(Package::class, 2)->create();
        factory(Package::class)->create(['is_disabled' => true]);

        $apiCall = $this->get('api/stats')->json();

        $this->assertEquals(2, $apiCall['package_count']);
    }

    /** @test */
    public function it_sums_live_package_download_counts()
    {
        factory(Package::class)->create(['packagist_downloads' => 123]);
        factory(Package::class)->create(['packagist_downloads' => 234]);
        factory(Package::class)->create(['is_disabled' => true, 'packagist_downloads' => 999]);

        $apiCall = $this->get('api/stats')->json();

        $this->assertEquals(123 + 234, $apiCall['packagist_download_count']);
    }

    /** @test */
    public function it_sums_live_package_star_counts()
    {
        factory(Package::class)->create(['github_stars' => 123]);
        factory(Package::class)->create(['github_stars' => 234]);
        factory(Package::class)->create(['is_disabled' => true, 'github_stars' => 999]);

        $apiCall = $this->get('api/stats')->json();

        $this->assertEquals(123 + 234, $apiCall['github_star_count']);
    }

    /** @test */
    public function it_counts_collaborators()
    {
        factory(Collaborator::class, 4)->create();

        $apiCall = $this->get('api/stats')->json();

        $this->assertEquals(4, $apiCall['collaborator_count']);
    }

    /** @test */
    public function it_counts_ratings()
    {
        $this->markTestIncomplete('Waiting on latest PR to merge');
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $package1 = factory(User::class)->create();
        $package2 = factory(User::class)->create();
        $package3 = factory(User::class)->create();

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
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $package1 = factory(User::class)->create();
        $package2 = factory(User::class)->create();
        $package3 = factory(User::class)->create();

        $user1->ratePackage($package1, 5);
        $user1->ratePackage($package2, 3);
        $user1->ratePackage($package3, 1);

        $user2->ratePackage($package1, 4);
        $user2->ratePackage($package2, 2);

        $apiCall = $this->get('api/stats')->json();

        $this->assertEquals(3, $apiCall['rating_count']);
    }
}
