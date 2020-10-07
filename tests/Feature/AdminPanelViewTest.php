<?php

namespace Tests\Feature;

use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelViewTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->state('admin')->create();
    }

    /** @test */
    public function admin_panel_shows_enabled_packages_if_there_are_any()
    {
        $enabledPackage = factory(Package::class)->create();

        $this->be($this->user)
            ->get(route('app.admin.index'))
            ->assertViewHas('enabled_packages', function ($enabled_packages) use ($enabledPackage) {
                return $enabled_packages->contains($enabledPackage);
            });
    }

    /** @test */
    public function admin_panel_shows_disabled_packages_if_there_are_any()
    {
        $disabledPackage = factory(Package::class)->states('disabled')->create();

        $this->be($this->user)
            ->get(route('app.admin.index'))
            ->assertViewHas('disabled_packages', function ($disabled_packages) use ($disabledPackage) {
                return $disabled_packages->contains($disabledPackage);
            });
    }

    /** @test */
    public function admin_panel_does_not_show_disabled_package_list_if_there_are_none()
    {
        $enabledPackage = factory(Package::class)->create();

        $this->be($this->user)
            ->get(route('app.admin.index'))
            ->assertDontSee('Disabled Packages');
    }

    /** @test */
    public function admin_panel_does_not_show_enabled_package_list_if_there_are_none()
    {
        $disabledPackage = factory(Package::class)->states('disabled')->create();

        $this->be($this->user)
            ->get(route('app.admin.index'))
            ->assertDontSee('Enabled Packages');
    }

    /** @test */
    public function admin_user_can_view_individual_page_for_disabled_package()
    {
        $disabledPackage = factory(Package::class)->states('disabled')->create();

        $this->be($this->user)
            ->get(route('packages.show', [
                'namespace' => $disabledPackage->composer_vendor,
                'name' => $disabledPackage->composer_package,
            ]))
            ->assertOk();
    }

    /** @test */
    public function admin_user_can_view_edit_page_for_disabled_package()
    {
        $disabledPackage = factory(Package::class)->states('disabled')->create();

        $this->be($this->user)
            ->get(route('app.packages.edit', $disabledPackage->id))
            ->assertOk();
    }
}
