<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Package;
use App\Screenshot;
use App\Tag;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PackageCreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_attach_screenshots_to_the_package()
    {
        $this->withoutEvents();
        $this->fakesRepoFromRequest();

        $user = User::factory()->create();
        list($screenshotA, $screenshotB) = Screenshot::factory(2)->create(['uploader_id' => $user->id]);
        $validPackageData = array_merge($package = Package::factory()->make()->toArray(), [
            'packagist_namespace' => explode('/', $package['composer_name'])[0],
            'packagist_name' => explode('/', $package['composer_name'])[1],
        ]);

        $response = $this->actingAs($user)->post(route('app.packages.store'), array_merge($validPackageData, [
            'screenshots' => [
                $screenshotA->id,
                $screenshotB->id,
            ],
        ]));

        $packageScreenshots = Package::first()->screenshots;
        $this->assertCount(2, $packageScreenshots);
        $this->assertTrue($packageScreenshots->contains($screenshotA));
        $this->assertTrue($packageScreenshots->contains($screenshotB));
        $response->assertRedirect(route('app.packages.index'));
    }

    /** @test */
    public function screenshots_are_optional()
    {
        $this->withoutEvents();
        $this->fakesRepoFromRequest();

        $user = User::factory()->create();
        $validPackageData = array_merge($package = Package::factory()->make()->toArray(), [
            'packagist_namespace' => explode('/', $package['composer_name'])[0],
            'packagist_name' => explode('/', $package['composer_name'])[1],
        ]);

        $response = $this->actingAs($user)->post(route('app.packages.store'), $validPackageData);

        $this->assertCount(1, Package::all());
        $this->assertCount(0, Package::first()->screenshots);
        $response->assertRedirect(route('app.packages.index'));
    }

    /** @test */
    public function screenshots_must_be_an_array()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('app.packages.store'), [
            'screenshots' => 'not-an-array',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('screenshots');
    }

    /** @test */
    public function can_not_attach_more_than_20_screenshots()
    {
        $user = User::factory()->create();
        $screenshots = Screenshot::factory(21)->create(['uploader_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('app.packages.store'), [
            'screenshots' => $screenshots->pluck('id'),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('screenshots');
    }

    /** @test */
    public function all_uploaded_screenshots_are_returned_when_validation_fails()
    {
        $user = User::factory()->create();
        list($screenshotA, $screenshotB) = Screenshot::factory(2)->create(['uploader_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('app.packages.store'), [
            'packagist_namespace' => null,
            'screenshots' => [
                $screenshotA->id,
                $screenshotB->id,
            ],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('errors');
        $this->assertArraySubset([
            ['id' => $screenshotA->id, 'public_url' => Storage::url($screenshotA->path)],
            ['id' => $screenshotB->id, 'public_url' => Storage::url($screenshotB->path)],
        ], session('_old_input.screenshots'));
    }

    /** @test */
    public function the_selected_author_is_returned_to_the_view_when_validation_fails()
    {
        $this->withoutEvents();

        $user = User::factory()->create();
        $author = Collaborator::factory()->create();

        $response = $this->actingAs($user)->post(route('app.packages.store'), [
            'packagist_namespace' => null,
            'author_id' => $author->id,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('errors');
        $this->assertNotNull(old('selectedAuthor'), 'Expected selectedAuthor is missing from the session');
        $this->assertTrue(old('selectedAuthor')->is($author));
    }

    /** @test */
    public function the_selected_collaborators_are_returned_to_the_view_when_validation_fails()
    {
        $this->withoutEvents();

        $user = User::factory()->create();
        list($selectedCollaboratorA, $author, $selectedCollaboratorB) = Collaborator::factory(3)->create();
        $unselectedCollaborator = Collaborator::factory()->create();

        $response = $this->actingAs($user)->post(route('app.packages.store'), [
            'packagist_namespace' => null,
            'author_id' => $author->id,
            'contributors' => [
                $selectedCollaboratorA->id,
                $selectedCollaboratorB->id,
            ],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('errors');
        $this->assertNotNull(old('selectedCollaborators'), 'Expected selectedCollaborators is missing from the session');

        tap(old('selectedCollaborators'), function ($sessionCollaborators) use ($selectedCollaboratorA, $selectedCollaboratorB, $unselectedCollaborator) {
            $this->assertCount(2, $sessionCollaborators);
            $this->assertTrue($sessionCollaborators->contains($selectedCollaboratorA));
            $this->assertTrue($sessionCollaborators->contains($selectedCollaboratorB));
            $this->assertFalse($sessionCollaborators->contains($unselectedCollaborator));
            $this->assertEquals(array_keys($sessionCollaborators->toArray()), range(0, count($sessionCollaborators) - 1), 'Failed asserting $sessionCollaborator keys are sequential integers');
        });
    }

    /** @test */
    public function the_selected_existing_tags_and_new_tags_are_returned_to_the_view_when_validation_fails()
    {
        $this->withoutEvents();

        $user = User::factory()->create();
        $newTagName = 'New Tag';
        $selectedTags = collect([
            $tagA = Tag::factory()->create(['name' => 'Tag A']),
            $tagB = Tag::factory()->create(['name' => 'Tag B']),
            ['name' => $newTagName],
        ]);
        Tag::factory()->create(['name' => 'Excluded Tag']);

        $response = $this->actingAs($user)->post(route('app.packages.store'), [
            'packagist_namespace' => null,
            'tags' => [
                $tagA->id,
                $tagB->id,
            ],
            'tags-new' => [
                $newTagName,
            ],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('errors');
        $this->assertNotNull(old('selectedTags'), 'Expected selectedTags is missing from the session');
        old('selectedTags')->assertEquals($selectedTags);
    }

    /** @test */
    public function relative_urls_are_formatted_to_the_latest_release()
    {
        $this->withoutEvents();

        Http::fake([
            'https://packagist.org/packages/starwars/lightsabers.json' =>
                Http::response([
                    'package' => [
                        'repository' => 'https://github.com/starwars/lightsabers',
                        'versions' => [

                        ],
                    ],
                ]),
            'https://api.github.com/repos/starwars/lightsabers/readme' =>
                Http::response('<div id="readme" class="md" data-path="README.md"><article class="markdown-body entry-content p-5" itemprop="text"><p>Finding a <a href="kyber-crystal">kyber crystal</a> for your lightsaber</p></article></div>'),
            'https://api.github.com/repos/starwars/lightsabers/releases' =>
                Http::response([
                    [
                        'name' => 'Release',
                        'tag_name' => 'v1.0',
                    ],
                ]),
        ]);

        $user = User::factory()->create();
        $author = Collaborator::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->post(route('app.packages.store'), [
            'author_id' => $author->id,
            'name' => 'Lightsabers',
            'packagist_namespace' => 'starwars',
            'packagist_name' => 'lightsabers',
            'url' => 'https://github.com/starwars/lightsabers',
            'abstract' => 'This is a test package',
        ]);

        $this->assertDatabaseHas('packages', ['name' => 'Lightsabers']);

        $response = $this->get(route('packages.show', [
            'namespace' => 'starwars',
            'name' => 'lightsabers',
        ]));
        $readme = $response->viewData('package')['readme'];

        // The github link should be based on the latest release tag rather than the release name
        $this->assertStringNotContainsStringIgnoringCase(
            'https://github.com/starwars/lightsabers/blob/Release/kyber-crystal',
            $readme
        );
        $this->assertStringContainsStringIgnoringCase(
            'https://github.com/starwars/lightsabers/blob/v1.0/kyber-crystal',
            $readme
        );
    }

    /** @test */
    public function an_existing_tag_is_used_if_the_tag_submitted_differs_only_in_case()
    {
        $this->withoutExceptionHandling();
        $this->withoutEvents();
        $this->fakesRepoFromRequest();

        $existingTagA = Tag::factory()->create(['name' => 'test tag a', 'slug' => 'test-tag-a']);
        $existingTagB = Tag::factory()->create(['name' => 'test tag b', 'slug' => 'test-tag-b']);
        $user = User::factory()->create();
        $validPackageData = array_merge($package = Package::factory()->make()->toArray(), [
            'packagist_namespace' => explode('/', $package['composer_name'])[0],
            'packagist_name' => explode('/', $package['composer_name'])[1],
        ]);

        $response = $this->actingAs($user)->post(route('app.packages.store'), array_merge($validPackageData, [
            'tags-new' => [
                'Test tag A',
                'Test tag B',
            ],
        ]));

        $this->assertCount(2, Tag::all());
        $this->assertTrue(Package::first()->tags->contains($existingTagA));
        $this->assertTrue(Package::first()->tags->contains($existingTagB));
        $response->assertRedirect(route('app.packages.index'));
    }

    /** @test */
    public function an_existing_tag_is_used_if_the_tag_submitted_differs_only_in_case_and_a_new_tag_is_added()
    {
        $this->withoutExceptionHandling();
        $this->withoutEvents();
        $this->fakesRepoFromRequest();

        $existingTag = Tag::factory()->create(['name' => 'test tag', 'slug' => 'test-tag']);
        $user = User::factory()->create();
        $validPackageData = array_merge($package = Package::factory()->make()->toArray(), [
            'packagist_namespace' => explode('/', $package['composer_name'])[0],
            'packagist_name' => explode('/', $package['composer_name'])[1],
        ]);

        $response = $this->actingAs($user)->post(route('app.packages.store'), array_merge($validPackageData, [
            'tags-new' => [
                'Test tag',
                'New Tag',
                'Another New Tag',
            ],
        ]));

        $this->assertCount(3, Tag::all());
        $this->assertTrue(Package::first()->tags->contains($existingTag));
        $this->assertTrue(Package::first()->tags->contains(Tag::where('name', 'new tag')->first()));
        $this->assertTrue(Package::first()->tags->contains(Tag::where('name', 'another new tag')->first()));
        $response->assertRedirect(route('app.packages.index'));
    }
}
