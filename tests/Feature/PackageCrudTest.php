<?php

namespace Tests\Feature;

use App\Events\PackageCreated;
use App\Listeners\SendNewPackageNotification;
use App\Models\Collaborator;
use App\Models\Favorite;
use App\Models\Package;
use App\Models\Review;
use App\Models\Screenshot;
use App\Models\Tag;
use App\Models\User;
use App\Notifications\NewPackage;
use App\Notifications\PackageDeleted;
use App\Tighten;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use willvincent\Rateable\Rating;

class PackageCrudTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function app_package_index_shows_my_packages()
    {
        $user = User::factory()->create();
        $collaborator = Collaborator::factory()->create();
        $user->collaborators()->save($collaborator);

        $package = $collaborator->authoredPackages()->save(Package::factory()->make());

        $response = $this->be($user)->get(route('app.packages.index'));

        $response->assertSee($package->name);
        $response->assertSee(route('packages.show', [
            'namespace' => $package->composer_vendor,
            'name' => $package->composer_package,
        ]));
        $response->assertSee(route('app.packages.edit', $package));
    }

    /** @test */
    public function app_package_index_doesnt_show_others_packages()
    {
        $this->markTestIncomplete('Needs to scope just to the "My Packages" section');

        $user1 = User::factory()->create();
        $collaborator1 = Collaborator::factory()->create();
        $user1->collaborators()->save($collaborator1);

        $collaborator2 = Collaborator::factory()->create();

        $package = $collaborator2->authoredPackages()->save(Package::factory()->make());

        $response = $this->be($user1)->get(route('app.packages.index'));

        $response->assertDontSee($package->name);
    }

    /** @test */
    public function app_package_index_shows_my_favorited_packages()
    {
        $userA = User::factory()->create();
        $userAFavorites = Favorite::factory(2)->create([
            'user_id' => $userA->id,
        ]);
        $userB = User::factory()->create();
        $userBFavorite = Favorite::factory()->create([
            'user_id' => $userB->id,
        ]);

        $response = $this->actingAs($userA)->get(route('app.packages.index'));

        $response->assertSuccessful();
        $response->assertViewHas('favoritePackages', function ($viewFavorites) use ($userAFavorites, $userBFavorite) {
            return $viewFavorites->diff($userAFavorites)->isEmpty()
                && $viewFavorites->contains($userBFavorite) === false;
        });
    }

    /** @test */
    public function authenticated_user_can_see_create_package_page()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('app.packages.create'))
            ->assertOk()
            ->assertSee('Submit Package');
    }

    /** @test */
    public function non_authenticated_user_cannot_see_create_package_page()
    {
        $this->get(route('app.packages.create'))
            ->assertRedirect('/login/github');
    }

    /** @test */
    public function user_can_submit_a_package()
    {
        Event::fake();

        $existingTag = Tag::factory()->create();
        $user = User::factory()->create();
        $collaborator = Collaborator::factory()->make();
        $user->collaborators()->save($collaborator);
        $packageData = Package::factory()->make([
            'author_id' => $user->collaborators->first()->id,
            'url' => 'https://www.example.com/tightenco/bae',
        ]);
        $formData = array_merge($this->postFromPackage($packageData), [
            'tags-new' => ['New tag'],
            'tags' => [$existingTag->id],
        ]);
        $source = 'fake-repo-source';
        $readme = '# Fake Repo Readme';
        $latestVersion = 'v1.2.3';
        $this->fakesRepoFromRequest([
            'url' => $formData['url'],
            'source' => $source,
            'readme' => $readme,
            'latest_version' => $latestVersion,
        ]);

        $this->actingAs($user)
            ->followingRedirects()
            ->post(route('app.packages.store'), $formData)
            ->assertSuccessful();

        Event::assertDispatched(PackageCreated::class);
        $this->assertCount(1, Package::all());
        $package = Package::first();
        $this->assertEquals($formData['name'], $package->name);
        $this->assertEquals($formData['author_id'], $package->author_id);
        $this->assertEquals($formData['url'], $package->url);
        $this->assertEquals($formData['abstract'], $package->abstract);
        $this->assertEquals($formData['instructions'], $package->instructions);
        $this->assertEquals($packageData->composer_name, $package->composer_name);
        $this->assertEquals($formData['url'], $package->repo_url);
        $this->assertEquals($source, $package->readme_source);
        $this->assertEquals($readme, $package->readme);
        $this->assertEquals($latestVersion, $package->latest_version);
        $this->assertTrue($package->tags->contains('id', Tag::where('slug', 'new-tag')->first()->id));
        $this->assertTrue($package->tags->contains('id', $existingTag->id));
    }

    /** @test */
    public function non_user_cannot_submit_a_package()
    {
        $package = Package::factory()->make();
        $this->followingRedirects()
            ->post(route('app.packages.store'), $this->postFromPackage($package))
            ->assertNotFound();

        $this->assertDatabaseMissing('packages', ['name' => $package->name]);
    }

    /** @test */
    public function user_cannot_submit_package_without_author()
    {
        $user = User::factory()->create();
        $package = Package::factory()->make(['author_id' => null]);

        $this->actingAs($user)
            ->post(route('app.packages.store'), $this->postFromPackage($package))
            ->assertSessionHasErrors(null, 'The given data was invalid.');

        $this->assertDatabaseMissing('packages', ['name' => $package->name]);
    }

    /** @test */
    public function user_cannot_submit_package_without_package_name()
    {
        $user = User::factory()->create();
        $package = Package::factory()->make(['name' => null]);

        $this->actingAs($user)
            ->post(route('app.packages.store'), $this->postFromPackage($package))
            ->assertSessionHasErrors(null, 'The given data was invalid.');

        $this->assertDatabaseMissing('packages', ['name' => $package->name]);
    }

    /** @test */
    public function user_cannot_submit_package_without_packagist_namespace()
    {
        $user = User::factory()->create();
        $package = Package::factory()->make(['composer_name' => '/test-name']);

        $this->actingAs($user)
            ->post(route('app.packages.store'), $this->postFromPackage($package))
            ->assertSessionHasErrors(null, 'The given data was invalid.');

        $this->assertDatabaseMissing('packages', ['name' => $package->name]);
    }

    /** @test */
    public function user_cannot_submit_package_without_packagist_name()
    {
        $user = User::factory()->create();
        $package = Package::factory()->make(['composer_name' => 'testing/']);

        $this->actingAs($user)
            ->post(route('app.packages.store'), $this->postFromPackage($package))
            ->assertSessionHasErrors(null, 'The given data was invalid.');

        $this->assertDatabaseMissing('packages', ['name' => $package->name]);
    }

    /** @test */
    public function user_cannot_submit_package_without_url()
    {
        $user = User::factory()->create();
        $package = Package::factory()->make(['url' => null]);

        $this->actingAs($user)
            ->post(route('app.packages.store'), $this->postFromPackage($package))
            ->assertSessionHasErrors(null, 'The given data was invalid.');

        $this->assertDatabaseMissing('packages', ['name' => $package->name]);
    }

    /** @test */
    public function user_cannot_submit_package_with_duplicate_packagist_name()
    {
        $this->withoutEvents();
        $this->fakesRepoFromRequest();

        $user = User::factory()->create();
        $originalPackage = Package::factory()->make();
        $duplicatePackage = Package::factory()->make(['composer_name' => $originalPackage->composer_name]);

        $this->actingAs($user)
            ->post(route('app.packages.store'), $this->postfromPackage($originalPackage));

        $this->assertDatabaseHas('packages', ['name' => $originalPackage->name]);

        $this->actingAs($user)
            ->post(route('app.packages.store'), $this->postFromPackage($duplicatePackage))
            ->assertSessionHasErrors('packagist_name', 'The package '.$originalPackage->composer_name.' has already been submitted.');
    }

    /** @test */
    public function it_sends_a_slack_notification_when_a_new_package_is_created()
    {
        Event::fake();
        $this->fakesRepoFromRequest();

        $user = User::factory()->create();
        $collaborator = Collaborator::factory()->create();
        $user->collaborators()->save($collaborator);
        $package = Package::factory()->make([
            'author_id' => $user->collaborators()->first()->id,
        ]);

        $this->actingAs($user)
            ->post(route('app.packages.store'), $this->postFromPackage($package));

        Event::assertDispatched(PackageCreated::class, function ($e) use ($package) {
            return $e->package->name === $package->name;
        });
    }

    /** @test */
    public function slack_notification_contains_submitted_users_name_if_submitted_user_is_not_author()
    {
        Notification::fake();
        Event::fake();
        $this->fakesRepoFromRequest();

        $user = User::factory()->create();
        $package = Package::factory()->make();

        $this->actingAs($user)->post(route('app.packages.store'), $this->postFromPackage($package));

        (new SendNewPackageNotification)->handle(new PackageCreated($package));

        Notification::assertSentTo(
            new Tighten,
            NewPackage::class,
            function ($notification, $channels) use ($user) {
                return $notification->toSlack(new Tighten)->attachments[0]->fields['Created By'] == $user->name;
            }
        );
    }

    /** @test */
    public function slack_notification_does_not_contain_submitted_users_name_if_submitted_user_is_author()
    {
        Notification::fake();
        Event::fake();
        $this->fakesRepoFromRequest();

        $collaborator = Collaborator::factory()->create();
        $user = User::factory()->create();
        $user->collaborators()->save($collaborator);

        $package = Package::factory()->make([
            'author_id' => $collaborator->id,
        ]);

        $this->actingAs($user)->post(route('app.packages.store'), $this->postFromPackage($package));

        (new SendNewPackageNotification)->handle(new PackageCreated($package));

        Notification::assertSentTo(
            new Tighten,
            NewPackage::class,
            function ($notification, $channels) {
                return ! Arr::has($notification->toSlack(new Tighten)->attachments[0]->fields, 'Created By');
            }
        );
    }

    /** @test */
    public function author_can_delete_their_packages()
    {
        $this->withoutEvents();

        Storage::fake();

        $authorUser = User::factory()->create();
        $authorCollaborator = Collaborator::factory()->create();
        $authorUser->collaborators()->save($authorCollaborator);
        $package = Package::factory()->create([
            'author_id' => $authorCollaborator->id,
        ]);

        $tag = Tag::factory()->create();
        $package->tags()->save($tag);

        $screenshot = Screenshot::factory()->create([
            'uploader_id' => $authorUser->id,
            'path' => File::create('screenshot.jpg')->store('screenshots'),
            'package_id' => $package,
        ]);

        $review = Review::factory()->create(['package_id' => $package]);

        $fanOfPackage = User::factory()->create();
        $fanOfPackage->ratePackage($package->id, 5);
        $rating = Rating::where([
            'user_id' => $fanOfPackage->id,
            'rateable_type' => Package::class,
            'rateable_id' => $package->id,
        ])->first();

        $favorite = $fanOfPackage->favoritePackage($package->id);

        $this->actingAs($authorUser)
            ->delete(route('app.packages.delete', $package))
            ->assertRedirect(route('app.packages.index'))
            ->assertSessionHas('status');

        $this->assertModelMissing($package);
        $this->assertModelMissing($screenshot);
        $this->assertModelMissing($review);
        $this->assertModelMissing($rating);
        $this->assertModelMissing($favorite);
    }

    /** @test */
    public function collaborators_can_delete_their_packages()
    {
        $this->withoutEvents();

        $user = User::factory()->create();
        $collaborator = Collaborator::factory()->create();
        $user->collaborators()->save($collaborator);

        $package = Package::factory()->create();
        $package->contributors()->sync($collaborator);

        $this->actingAs($user)
            ->delete(route('app.packages.delete', $package))
            ->assertRedirect(route('app.packages.index'))
            ->assertSessionHas('status');

        $this->assertModelMissing($package);
    }

    /** @test */
    public function submitter_can_delete_package()
    {
        $this->withoutEvents();

        $submitter = User::factory()->create();
        $package = Package::factory()->create(['submitter_id' => $submitter->id]);

        $this->actingAs($submitter)
            ->delete(route('app.packages.delete', $package))
            ->assertRedirect(route('app.packages.index'))
            ->assertSessionHas('status');

        $this->assertModelMissing($package);
    }

    /** @test */
    public function submitter_can_delete_package_if_package_author_is_not_a_user()
    {
        $this->withoutEvents();

        $submitter = User::factory()->create();
        $authorUser = User::factory()->create();
        $authorCollaborator = Collaborator::factory()->create();
        $authorUser->collaborators()->save($authorCollaborator);
        $package = Package::factory()->create([
            'author_id' => $authorCollaborator->id,
            'submitter_id' => $submitter->id,
        ]);

        $this->actingAs($submitter)
            ->delete(route('app.packages.delete', $package))
            ->assertStatus(403);

        $this->assertModelExists($package);
    }

    /** @test */
    public function admin_can_delete_package()
    {
        $this->withoutEvents();

        $admin = User::factory()->admin()->create();

        $package = Package::factory()->create();

        $this->actingAs($admin)
            ->delete(route('app.packages.delete', $package))
            ->assertRedirect(route('app.packages.index'))
            ->assertSessionHas('status');

        $this->assertModelMissing($package);
    }

    /** @test */
    public function users_that_are_not_a_packages_author_cannot_delete_it()
    {
        $user = User::factory()->create();
        $package = Package::factory()->create();

        $this->actingAs($user)
            ->delete(route('app.packages.delete', $package))
            ->assertStatus(403);

        $this->assertModelExists($package);
    }

    /** @test */
    public function deleting_package_fires_slack_notification()
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();

        $package = Package::factory()->create();

        $this->actingAs($admin)->delete(route('app.packages.delete', $package));

        Notification::assertSentTo(
            new Tighten,
            PackageDeleted::class,
            function ($notification, $channels) use ($admin, $package) {
                return $channels === ['slack']
                    && $notification->packageName === $package->name
                    && $notification->actor === $admin;
            }
        );
    }

    private function postFromPackage($package)
    {
        $packagistInformation = explode('/', $package->composer_name);

        return [
            'author_id' => $package->author_id,
            'name' => $package->name,
            'packagist_namespace' => $packagistInformation[0],
            'packagist_name' => $packagistInformation[1],
            'url' => $package->url,
            'description' => $package->description,
            'abstract' => $package->abstract,
            'instructions' => $package->instructions,
        ];
    }
}
