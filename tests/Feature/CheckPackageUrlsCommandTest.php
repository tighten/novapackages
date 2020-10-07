<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Package;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\NotifyContributorOfInvalidPackageUrl;

class CheckPackageUrlsCommandTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
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

        $this->packageWithInvalidURL = factory(Package::class)->create([
            'name' => 'Package with Invalid URL',
            'url' => 'https://github.com/some-dev/package-name',
            'repo_url' => 'https://github.com/some-dev/package-name',
            'author_id' => factory(Collaborator::class)->create([
                'user_id' => factory(User::class)->create()->id
            ])->id
        ]);

        $this->packageWithInvalidRepoURL = factory(Package::class)->create([
            'name' => 'Package with Invalid Repo URL',
            'url' => 'https://github.com/tighten/novapackages',
            'repo_url' => 'https://github.com/tighten/mispelled-package-name',
        ]);

        $this->packageWithInvalidDomain = factory(Package::class)->create([
            'name' => 'Package with Invalid Domain',
            'url' => 'https://not-github.com/other-dev/package-name',
            'repo_url' => 'https://github.com/tighten/novapackages',
        ]);
    }

    /**
     * @test
     */
    public function calling_command_tags_invalid_packages_with_404_tag()
    {
        $this->artisan('check:package-urls');

        $this->assertEquals($this->validPackage->tags()->count(), 0);
        $this->assertFalse($this->validPackage->tags()->where('tags.name', '404 error')->exists());
        $this->get(route(
            'packages.show',
            [
                $this->validPackage->composer_vendor,
                $this->validPackage->composer_package
            ]
        ))->assertDontSee('404 Error');

        $this->assertEquals($this->packageWithInvalidURL->tags()->count(), 1);
        $this->assertTrue($this->packageWithInvalidURL->tags()->where('tags.name', '404 error')->exists());
        $this->get(route(
            'packages.show',
            [
                $this->packageWithInvalidURL->composer_vendor,
                $this->packageWithInvalidURL->composer_package
            ]
        ))->assertSee('404 Error');

        $this->assertEquals($this->packageWithInvalidRepoURL->tags()->count(), 1);
        $this->assertTrue($this->packageWithInvalidRepoURL->tags()->where('tags.name', '404 error')->exists());
        $this->get(route(
            'packages.show',
            [
                $this->packageWithInvalidRepoURL->composer_vendor,
                $this->packageWithInvalidRepoURL->composer_package
            ]
        ))->assertSee('404 Error');

        $this->assertEquals($this->packageWithInvalidDomain->tags()->count(), 1);
        $this->assertTrue($this->packageWithInvalidDomain->tags()->where('tags.name', '404 error')->exists());
        $this->get(route(
            'packages.show',
            [
                $this->packageWithInvalidDomain->composer_vendor,
                $this->packageWithInvalidDomain->composer_package
            ]
        ))->assertSee('404 Error');
    }

    /**
     * @test
     */
    public function calling_command_sends_notification_to_author_and_contributors_of_invalid_packages()
    {
        Notification::fake();

        $collaboratorWithValidPackage = factory(Collaborator::class)->create([
            'user_id' => factory(User::class)->create()->id
        ]);
        $this->validPackage->contributors()->sync($collaboratorWithValidPackage);

        $this->packageWithInvalidURL->update([]);
        $contributorsWithInvalidPackage = factory(Collaborator::class, 2)->create([
            'user_id' => factory(User::class)->create()->id
        ]);
        $this->packageWithInvalidURL->contributors()->sync($contributorsWithInvalidPackage);

        $this->artisan('check:package-urls');

        Notification::assertNotSentTo(
            $this->validPackage->author->user,
            NotifyContributorOfInvalidPackageUrl::class
        );

        Notification::assertNotSentTo(
            $collaboratorWithValidPackage->user,
            NotifyContributorOfInvalidPackageUrl::class
        );

        Notification::assertSentTo(
            $this->packageWithInvalidURL->author->user,
            NotifyContributorOfInvalidPackageUrl::class,
        );

        foreach ($contributorsWithInvalidPackage as $contributor) {
            Notification::assertSentTo(
                $contributor->user,
                NotifyContributorOfInvalidPackageUrl::class,
            );
        }
    }
}
