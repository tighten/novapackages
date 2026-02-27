<?php

use App\Models\Collaborator;
use App\Models\Package;
use App\Models\User;
use App\Notifications\NotifyAuthorOfUnavailablePackageUrl;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

beforeEach(function () {
    $this->validPackage = Package::factory()->create([
        'name' => 'Valid Package',
        'url' => 'https://github.com/tighten/novapackages',
        'repo_url' => 'https://github.com/tighten/novapackages',
        'author_id' => Collaborator::factory()->create([
            'user_id' => User::factory()->create()->id,
        ])->id,
    ]);

    $this->packageWithUnavailableUrl = Package::factory()->create([
        'name' => 'Package with Unavailable URL',
        'url' => 'https://github.com/some-dev/package-name',
        'repo_url' => 'https://github.com/some-dev/package-name',
        'author_id' => Collaborator::factory()->create([
            'user_id' => User::factory()->create()->id,
        ])->id,
    ]);

    $this->packageWithUnavailableDomain = Package::factory()->create([
        'name' => 'Package with Unavailable Domain',
        'url' => 'https://not-github.com/other-dev/package-name',
        'repo_url' => 'https://github.com/tighten/novapackages',
    ]);
});


test('calling command marks unavailable packages as unavailable', function () {
    Notification::fake();
    $now = now();
    Carbon::setTestNow($now);
    $this->artisan('novapackages:check-package-urls');

    expect($this->validPackage->marked_as_unavailable_at)->toBeNull();
    $this->assertEquals(
        $this->packageWithUnavailableUrl->refresh()->marked_as_unavailable_at,
        $now
    );
    $this->assertEquals(
        $this->packageWithUnavailableDomain->refresh()->marked_as_unavailable_at,
        $now
    );
});

test('calling command sends notification to author of unavailable packages', function () {
    Notification::fake();

    Http::fake([
        $this->validPackage->url => Http::response(null, 200),
        $this->packageWithUnavailableUrl->url => Http::response(null, 404),
    ]);

    $this->artisan('novapackages:check-package-urls');

    Notification::assertNotSentTo(
        $this->validPackage->author->user,
        NotifyAuthorOfUnavailablePackageUrl::class
    );

    Notification::assertSentTo(
        $this->packageWithUnavailableUrl->author->user,
        NotifyAuthorOfUnavailablePackageUrl::class,
    );
});

test('command ignores packages that are already unavailable', function () {
    Notification::fake();

    $this->packageWithUnavailableUrl->marked_as_unavailable_at = now();
    $this->packageWithUnavailableUrl->save();

    $this->artisan('novapackages:check-package-urls');

    Notification::assertNotSentTo(
        $this->packageWithUnavailableUrl->author->user,
        NotifyAuthorOfUnavailablePackageUrl::class,
    );
});
