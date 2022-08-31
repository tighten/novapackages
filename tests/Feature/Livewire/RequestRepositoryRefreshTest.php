<?php

namespace Tests\Feature\Livewire;

use App\Collaborator;
use App\Http\Livewire\RequestRepositoryRefresh;
use App\Jobs\SyncPackageRepositoryData;
use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;
use Tests\TestCase;

class RequestRepositoryRefreshTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function only_admins_and_users_that_own_package_can_request_packagist_refresh()
    {
        $package = Package::factory()->create();

        $component = Livewire::test(RequestRepositoryRefresh::class, [
            'packageId' => $package->id,
        ]);

        $component->call('requestRefresh')->assertForbidden();

        $this->actingAs(User::factory()->admin()->create());

        $component->call('requestRefresh')->assertOk();
    }

    /** @test */
    function dispatches_job_to_sync_repository_data()
    {
        Bus::fake();

        $package = Package::factory()->make();
        $collaborator = Collaborator::factory()->make();
        $user = User::factory()->create();
        $user->collaborators()->save($collaborator);
        $collaborator->authoredPackages()->save($package);

        $this->actingAs($user);

        $component = Livewire::test(RequestRepositoryRefresh::class, [
            'packageId' => $package->id,
        ]);

        $component->call('requestRefresh')->assertOk();

        Bus::assertDispatched(SyncPackageRepositoryData::class, function ($job) use ($package) {
            return $job->package->is($package);
        });

        $this->assertTrue($component->refreshRequested);
    }
}
