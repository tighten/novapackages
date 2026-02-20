<?php

namespace Tests\Feature;

use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminPanelViewTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->admin()->create();
    }

    #[Test]
    public function admin_panel_shows_enabled_packages_if_there_are_any(): void
    {
        $enabledPackage = Package::factory()->create();

        $this->be($this->user)
            ->get(route('app.admin.index'))
            ->assertViewHas('enabled_packages', function ($enabled_packages) use ($enabledPackage) {
                return $enabled_packages->contains($enabledPackage);
            });
    }

    #[Test]
    public function admin_panel_shows_disabled_packages_if_there_are_any(): void
    {
        $disabledPackage = Package::factory()->disabled()->create();

        $this->be($this->user)
            ->get(route('app.admin.index'))
            ->assertViewHas('disabled_packages', function ($disabled_packages) use ($disabledPackage) {
                return $disabled_packages->contains($disabledPackage);
            });
    }

    #[Test]
    public function admin_panel_does_not_show_disabled_package_list_if_there_are_none(): void
    {
        $enabledPackage = Package::factory()->create();

        $this->be($this->user)
            ->get(route('app.admin.index'))
            ->assertDontSee('Disabled Packages');
    }

    #[Test]
    public function admin_panel_does_not_show_enabled_package_list_if_there_are_none(): void
    {
        $disabledPackage = Package::factory()->disabled()->create();

        $this->be($this->user)
            ->get(route('app.admin.index'))
            ->assertDontSee('Enabled Packages');
    }

    #[Test]
    public function admin_user_can_view_individual_page_for_disabled_package(): void
    {
        Http::fake([
            'https://packagist.org/packages/*.json' => Http::response(),
        ]);

        $disabledPackage = Package::factory()->disabled()->create();

        $this->be($this->user)
            ->get(route('packages.show', [
                'namespace' => $disabledPackage->composer_vendor,
                'name' => $disabledPackage->composer_package,
            ]))
            ->assertOk();
    }

    #[Test]
    public function admin_user_can_view_edit_page_for_disabled_package(): void
    {
        $disabledPackage = Package::factory()->disabled()->create();

        $this->be($this->user)
            ->get(route('app.packages.edit', $disabledPackage->id))
            ->assertOk();
    }
}
