<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Jobs\SyncPackageRepositoryData;
use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class UpdatePackageRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_request_a_refresh_of_a_packages_repository_data()
    {
        Bus::fake();

        $package = Package::factory()->make();
        $collaborator = Collaborator::factory()->make();
        $user = User::factory()->create();
        $user->collaborators()->save($collaborator);
        $collaborator->authoredPackages()->save($package);

        $response = $this->actingAs($user)->json('POST', route('app.packages.repository.refresh', $package));

        $response->assertSuccessful();
        Bus::assertDispatched(SyncPackageRepositoryData::class, function ($job) use ($package) {
            return $job->package->id === $package->id;
        });
    }

    /** @test */
    public function a_guest_user_can_not_request_a_refresh_of_a_packages_repository_data()
    {
        Bus::fake();

        $package = Package::factory()->create();

        $response = $this->json('POST', route('app.packages.repository.refresh', $package));

        $response->assertStatus(401);
        Bus::assertNotDispatched(SyncPackageRepositoryData::class);
    }
}
