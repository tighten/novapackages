<?php

namespace Tests\Feature\Livewire;

use App\CacheKeys;
use App\Collaborator;
use App\Http\Livewire\RequestPackagistCacheRefresh;
use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class RequestPackagistCacheRefreshTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function only_admins_and_users_that_own_package_can_request_packagist_refresh()
    {
        $package = Package::factory()->create();

        $component = Livewire::test(RequestPackagistCacheRefresh::class, [
            'composerName' => $package->composer_name,
            'packageId' => $package->id,
        ]);

        $component->call('requestRefresh')->assertForbidden();

        $this->actingAs(User::factory()->admin()->create());

        $component->call('requestRefresh')->assertOk();
    }

    /** @test */
    function removes_package_packagist_data_from_cache()
    {
        Cache::spy();

        $package = Package::factory()->make();
        $collaborator = Collaborator::factory()->make();
        $user = User::factory()->create();
        $user->collaborators()->save($collaborator);
        $collaborator->authoredPackages()->save($package);

        $this->actingAs($user);
        $component = Livewire::test(RequestPackagistCacheRefresh::class, [
            'composerName' => $package->composer_name,
            'packageId' => $package->id,
        ])->call('requestRefresh')->assertOk();

        Cache::shouldHaveReceived('forget')->once()->with(CacheKeys::packagistData($package->composer_name));

        $this->assertTrue($component->refreshRequested);
    }
}
