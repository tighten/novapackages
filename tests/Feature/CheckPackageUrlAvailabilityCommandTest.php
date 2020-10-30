<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Notifications\NotifyContributorOfUnavailablePackageUrl;
use App\Package;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/** @group integration */
class CheckPackageUrlAvailabilityCommandTest extends TestCase
{

    use RefreshDatabase;

    public function setUp():void
    {
        parent::setUp();
        $this->validPackage = factory(Package::class)->create([
            'name' => 'Valid Package',
            'url' => 'https://github.com/tighten/novapackages',
            'repo_url' => 'https://github.com/tighten/novapackages',
            'author_id' => factory(Collaborator::class)->create([
                'user_id' => factory(User::class)->create()->id
            ])->id
        ]);

        $this->packageWithUnavailableUrl = factory(Package::class)->create([
            'name' => 'Package with Unavailable URL',
            'url' => 'https://github.com/some-dev/package-name',
            'repo_url' => 'https://github.com/some-dev/package-name',
            'author_id' => factory(Collaborator::class)->create([
                'user_id' => factory(User::class)->create()->id
            ])->id
        ]);

        $this->packageWithUnavailableDomain = factory(Package::class)->create([
            'name' => 'Package with Unavailable Domain',
            'url' => 'https://not-github.com/other-dev/package-name',
            'repo_url' => 'https://github.com/tighten/novapackages',
        ]);
    }

    /**
     * @test
     */
    public function calling_command_marks_unavailable_packages_as_unavailable()
    {
        $now = now();
        Carbon::setTestNow($now);
        $this->artisan('novapackages:check-package-urls');

        $this->assertNull($this->validPackage->marked_as_unavailable_at);
        $this->assertEquals(
            $this->packageWithUnavailableUrl->refresh()->marked_as_unavailable_at,
            $now
        );
        $this->assertEquals(
            $this->packageWithUnavailableDomain->refresh()->marked_as_unavailable_at,
            $now
        );
    }

    /**
     * @test
     */
    public function calling_command_sends_notification_to_author_of_unavailable_packages()
    {
        Notification::fake();

        $this->artisan('novapackages:check-package-urls');

        Notification::assertNotSentTo(
            $this->validPackage->author->user,
            NotifyContributorOfUnavailablePackageUrl::class
        );

        Notification::assertSentTo(
            $this->packageWithUnavailableUrl->author->user,
            NotifyContributorOfUnavailablePackageUrl::class,
        );
    }

    /**
     * @test
     */
    public function command_ignores_packages_that_are_already_unavailable()
    {
        Notification::fake();

         $this->packageWithUnavailableUrl->marked_as_unavailable_at = now();
         $this->packageWithUnavailableUrl->save();

        $this->artisan('novapackages:check-package-urls');

        Notification::assertNotSentTo(
            $this->packageWithUnavailableUrl->author->user,
            NotifyContributorOfUnavailablePackageUrl::class,
        );
    }
}
