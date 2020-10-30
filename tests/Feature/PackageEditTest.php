<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Package;
use App\Screenshot;
use App\Tag;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PackageEditTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutEvents;

    /** @test */
    public function user_can_update_a_package()
    {
        list($package, $user) = $this->createPackageWithUser();
        $formData = [
            'name' => 'BAE Package',
            'author_id' => $user->id,
            'url' => 'https://www.example.com/tightenco/bae',
            'abstract' => 'This is the abstract fo the BAE package',
            'instructions' => 'This is how you install the BAE package',
            'packagist_namespace' => 'tightenco',
            'packagist_name' => 'bae',
        ];
        $source = 'fake-repo-source';
        $readme = '# Fake Repo Readme';
        $version = 'v1.2.3';
        $this->fakesRepoFromRequest([
            'url' =>$formData['url'],
            'source' => $source,
            'readme' => $readme,
            'latest_version' => $version,
        ]);
        $existingTag = factory(Tag::class)->create();
        $package->tags()->save($existingTag);
        $formData = array_merge($formData, [
            'tags-new' => ['New tag'],
            'tags' => [$existingTag->id],
        ]);

        $response = $this->actingAs($user)->put(route('app.packages.update', $package), $formData);

        $response->assertStatus(302);
        $this->assertCount(1, Package::all());
        $package = Package::first();
        $this->assertEquals($formData['name'], $package->name);
        $this->assertEquals($formData['author_id'], $package->author_id);
        $this->assertEquals($formData['url'], $package->url);
        $this->assertEquals($formData['abstract'], $package->abstract);
        $this->assertEquals($formData['instructions'], $package->instructions);
        $this->assertEquals("{$formData['packagist_namespace']}/{$formData['packagist_name']}", $package->composer_name);
        $this->assertEquals($formData['url'], $package->repo_url);
        $this->assertEquals($source, $package->readme_source);
        $this->assertEquals($readme, $package->readme);
        $this->assertEquals($version, $package->latest_version);
        $this->assertTrue($package->tags->contains('id', Tag::where('slug', 'new-tag')->first()->id));
        $this->assertTrue($package->tags->contains('id', $existingTag->id));
    }

    /** @test */
    public function an_authenticated_user_can_view_the_edit_package_page()
    {
        list($packageA, $user) = $this->createPackageWithUser();
        $screenshot = factory(Screenshot::class)->create(['uploader_id' => $user->id]);
        $packageA->screenshots()->save($screenshot);
        $packageB = factory(Package::class)->create();

        $response = $this->actingAs($user)->get(route('app.packages.edit', $packageA));

        $response->assertSuccessful();
        $response->assertViewHas('package');
        $response->assertViewHas('collaborators');
        $response->assertViewHas('tags');
        $response->assertViewHas('screenshots', function ($viewScreenshots) use ($screenshot) {
            return $viewScreenshots->count() === 1 && $viewScreenshots->first()['public_url'] == Storage::url($screenshot->path);
        });
    }

    /** @test */
    public function the_composer_name_must_be_unique()
    {
        $this->fakesRepoFromRequest();

        $existingPackage = factory(Package::class)->create(['composer_name' => 'tightenco/bae']);
        list($package, $user) = $this->createPackageWithUser();

        $response = $this->actingAs($user)->put(route('app.packages.update', $package), [
            'packagist_namespace' => 'tightenco',
            'packagist_name' => 'bae',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('packagist_name');
    }

    /** @test */
    public function the_composer_can_remain_the_same()
    {
        $this->fakesRepoFromRequest();

        $package = factory(Package::class)->create(['composer_name' => 'tightenco/bae']);
        $collaborator = factory(Collaborator::class)->make();
        $user = factory(User::class)->create();
        $user->collaborators()->save($collaborator);
        $collaborator->authoredPackages()->save($package);

        $response = $this->actingAs($user)->put(route('app.packages.update', $package), array_merge($package->toArray(), [
            'packagist_namespace' => 'tightenco',
            'packagist_name' => 'bae',
        ]));

        $response->assertSessionHasNoErrors();
    }

    /** @test */
    public function can_update_multiple_screenshots()
    {
        $this->fakesRepoFromRequest();

        list($package, $user) = $this->createPackageWithUser();
        list($oldScreenshot, $screenshotA, $screenshotB) = factory(Screenshot::class, 3)->create(['uploader_id' => $user->id]);
        $package->screenshots()->save($oldScreenshot);

        $response = $this->actingAs($user)->put(route('app.packages.update', $package), array_merge($this->getValidPackageData(), [
            'screenshots' => [
                $screenshotA->id,
                $screenshotB->id,
            ],
        ]));

        tap($package->fresh()->screenshots, function ($packageScreenshots) use ($oldScreenshot, $screenshotA, $screenshotB) {
            $this->assertCount(2, $packageScreenshots);
            $this->assertTrue($packageScreenshots->contains($screenshotA));
            $this->assertTrue($packageScreenshots->contains($screenshotB));
            $this->assertFalse($packageScreenshots->contains($oldScreenshot));
        });

        $response->assertRedirect(route('app.packages.index'));
    }

    /** @test */
    public function screenshots_are_optional()
    {
        $this->fakesRepoFromRequest();

        list($package, $user) = $this->createPackageWithUser();

        $response = $this->actingAs($user)->put(route('app.packages.update', $package), $this->getValidPackageData());

        $this->assertCount(0, $package->screenshots);
        $response->assertRedirect(route('app.packages.index'));
    }

    /** @test */
    public function can_not_upload_more_than_20_screenshots()
    {
        list($package, $user) = $this->createPackageWithUser();
        $screenshots = factory(Screenshot::class, 21)->create(['uploader_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('app.packages.update', $package), [
            'screenshots' => $screenshots->pluck('id'),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('screenshots');
    }

    /** @test */
    public function screenshots_must_be_an_array()
    {
        list($package, $user) = $this->createPackageWithUser();

        $response = $this->actingAs($user)->put(route('app.packages.update', $package), [
            'screenshots' => 'not-an-array',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('screenshots');
    }

    /** @test */
    public function all_uploaded_screenshots_are_returned_when_validation_fails()
    {
        list($package, $user) = $this->createPackageWithUser();
        list($oldScreenshot, $screenshotA, $screenshotB) = factory(Screenshot::class, 3)->create(['uploader_id' => $user->id]);
        $package->screenshots()->save($oldScreenshot);

        $response = $this->actingAs($user)->put(route('app.packages.update', $package), [
            'packagist_namespace' => null,
            'screenshots' => [$oldScreenshot->id, $screenshotA->id, $screenshotB->id],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('errors');
        $this->assertArraySubset(['id' => $oldScreenshot->id, 'public_url' => Storage::url($oldScreenshot->path)], session('_old_input.screenshots')[0]);
        $this->assertArraySubset(['id' => $screenshotA->id, 'public_url' => Storage::url($screenshotA->path)], session('_old_input.screenshots')[1]);
        $this->assertArraySubset(['id' => $screenshotB->id, 'public_url' => Storage::url($screenshotB->path)], session('_old_input.screenshots')[2]);
    }

    /** @test */
    public function the_selected_author_is_returned_to_the_view_when_validation_fails()
    {
        list($package, $user) = $this->createPackageWithUser();
        $author = factory(Collaborator::class)->create();

        $response = $this->actingAs($user)->put(route('app.packages.update', $package), [
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
        list($package, $user) = $this->createPackageWithUser();
        list($selectedCollaboratorA, $author, $selectedCollaboratorB) = factory(Collaborator::class, 3)->create();
        $unselectedCollaborator = factory(Collaborator::class)->create();

        $response = $this->actingAs($user)->put(route('app.packages.update', $package), [
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
        list($package, $user) = $this->createPackageWithUser();
        $newTagName = 'New Tag';
        $selectedTags = collect([
            $tagA = factory(Tag::class)->create(['name' => 'Tag A']),
            $tagB = factory(Tag::class)->create(['name' => 'Tag B']),
            ['name' => $newTagName],
        ]);
        factory(Tag::class)->create(['name' => 'Excluded Tag']);

        $response = $this->actingAs($user)->put(route('app.packages.update', $package), [
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
        $this->assertNotNull(old('selectedTags'), 'Expected selectedCollaborators is missing from the session');
        old('selectedTags')->assertEquals($selectedTags);
    }

    /** @test */
    public function an_existing_tag_is_used_if_the_tag_submitted_differs_only_in_case()
    {
        $this->withoutExceptionHandling();
        $this->fakesRepoFromRequest();

        list($package, $user) = $this->createPackageWithUser();
        $existingTagA = factory(Tag::class)->create(['name' => 'test tag a', 'slug' => 'test-tag-a']);
        $existingTagB = factory(Tag::class)->create(['name' => 'test tag b', 'slug' => 'test-tag-b']);

        $response = $this->actingAs($user)->put(route('app.packages.update', $package), [
            'name' => $this->faker->company,
            'author_id' => $user->id,
            'url' =>  $this->faker->url,
            'abstract' =>  $this->faker->sentence,
            'instructions' => $this->faker->sentence,
            'packagist_namespace' => $this->faker->word,
            'packagist_name' => $this->faker->word,
            'tags-new' => [
                'Test tag A',
                'Test tag B',
            ],
        ]);

        $this->assertCount(2, $package->tags);
        $this->assertTrue($package->tags->contains($existingTagA));
        $this->assertTrue($package->tags->contains($existingTagB));
        $response->assertRedirect(route('app.packages.index'));
    }

    /** @test */
    public function an_existing_tag_is_used_if_the_tag_submitted_differs_only_in_case_and_a_new_tag_is_added()
    {
        $this->withoutExceptionHandling();
        $this->fakesRepoFromRequest();

        list($package, $user) = $this->createPackageWithUser();
        $existingTag = factory(Tag::class)->create(['name' => 'test tag', 'slug' => 'test-tag']);

        $response = $this->actingAs($user)->put(route('app.packages.update', $package), [
            'name' => $this->faker->company,
            'author_id' => $user->id,
            'url' =>  $this->faker->url,
            'abstract' =>  $this->faker->sentence,
            'instructions' => $this->faker->sentence,
            'packagist_namespace' => $this->faker->word,
            'packagist_name' => $this->faker->word,
            'tags-new' => [
                'Test tag',
                'New Tag',
                'Another New Tag',
            ],
        ]);

        $this->assertCount(3, $package->tags);
        $this->assertTrue($package->tags->contains($existingTag));
        $this->assertTrue($package->tags->contains(Tag::where('name', 'new tag')->first()));
        $this->assertTrue($package->tags->contains(Tag::where('name', 'another new tag')->first()));
        $response->assertRedirect(route('app.packages.index'));
    }

    /** @test */
    public function not_updating_url_does_not_change_package_availability()
    {
        $this->withoutExceptionHandling();
        $this->fakesRepoFromRequest();

        list($package, $user) = $this->createPackageWithUser();

        $package->marked_as_unavailable_at = now();
        $package->is_disabled = true;
        $package->save();

        $this->actingAs($user)->put(route('app.packages.update', $package), [
            'name' => $this->faker->company,
            'author_id' => $user->id,
            'url' =>  $package->url,
            'abstract' =>  $this->faker->sentence,
            'packagist_namespace' => $this->faker->word,
            'packagist_name' => $this->faker->word,
        ])
        ->assertRedirect(route('app.packages.index'));

        $this->assertNotNull($package->refresh()->marked_as_unavailable_at);
        $this->assertTrue($package->refresh()->is_disabled);
    }

    /** @test */
    public function updating_url_attribute_removes_unavailable_timestamp()
    {
        $this->withoutExceptionHandling();
        $this->fakesRepoFromRequest();

        list($package, $user) = $this->createPackageWithUser();

        $package->marked_as_unavailable_at = now();
        $package->is_disabled = true;
        $package->save();

        $this->actingAs($user)->put(route('app.packages.update', $package), [
            'name' => $this->faker->company,
            'author_id' => $user->id,
            'url' =>  $this->faker->url,
            'abstract' =>  $this->faker->sentence,
            'packagist_namespace' => $this->faker->word,
            'packagist_name' => $this->faker->word,
        ])
            ->assertRedirect(route('app.packages.index'));

        $this->assertNull($package->refresh()->marked_as_unavailable_at);
        $this->assertFalse($package->refresh()->is_disabled);
    }

    private function getValidPackageData()
    {
        return array_merge(factory(Package::class)->make()->toArray(), [
            'packagist_namespace' => 'tightenco',
            'packagist_name' => 'bae',
        ]);
    }

    private function createPackageWithUser()
    {
        $package = factory(Package::class)->make();
        $collaborator = factory(Collaborator::class)->make();
        $user = factory(User::class)->create();
        $user->collaborators()->save($collaborator);
        $collaborator->authoredPackages()->save($package);
        $package->tags()->save(factory(Tag::class)->create());

        return [$package, $user];
    }
}
