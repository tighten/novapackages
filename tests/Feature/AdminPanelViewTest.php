<?php

use App\Models\Package;
use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->user = User::factory()->admin()->create();
});

test('admin panel shows enabled packages if there are any', function () {
    $enabledPackage = Package::factory()->create();

    $this->be($this->user)
        ->get(route('app.admin.index'))
        ->assertViewHas('enabled_packages', function ($enabled_packages) use ($enabledPackage) {
            return $enabled_packages->contains($enabledPackage);
        });
});

test('admin panel shows disabled packages if there are any', function () {
    $disabledPackage = Package::factory()->disabled()->create();

    $this->be($this->user)
        ->get(route('app.admin.index'))
        ->assertViewHas('disabled_packages', function ($disabled_packages) use ($disabledPackage) {
            return $disabled_packages->contains($disabledPackage);
        });
});

test('admin panel does not show disabled package list if there are none', function () {
    $enabledPackage = Package::factory()->create();

    $this->be($this->user)
        ->get(route('app.admin.index'))
        ->assertDontSee('Disabled Packages');
});

test('admin panel does not show enabled package list if there are none', function () {
    $disabledPackage = Package::factory()->disabled()->create();

    $this->be($this->user)
        ->get(route('app.admin.index'))
        ->assertDontSee('Enabled Packages');
});

test('admin user can view individual page for disabled package', function () {
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
});

test('admin user can view edit page for disabled package', function () {
    $disabledPackage = Package::factory()->disabled()->create();

    $this->be($this->user)
        ->get(route('app.packages.edit', $disabledPackage->id))
        ->assertOk();
});
