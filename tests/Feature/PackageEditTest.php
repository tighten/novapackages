<?php

use App\Models\Collaborator;
use App\Models\Package;
use App\Models\Screenshot;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

uses(WithFaker::class);

test('user can update a package', function () {
    [$package, $user] = createPackageWithUser();
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
        'url' => $formData['url'],
        'source' => $source,
        'readme' => $readme,
        'latest_version' => $version,
    ]);
    $existingTag = Tag::factory()->create();
    $package->tags()->save($existingTag);
    $formData = array_merge($formData, [
        'tags-new' => ['New tag'],
        'tags' => [$existingTag->id],
    ]);

    $response = $this->actingAs($user)->put(route('app.packages.update', $package), $formData);

    $response->assertStatus(302);
    expect(Package::all())->toHaveCount(1);
    $package = Package::first();
    expect($package->name)->toEqual($formData['name']);
    expect($package->author_id)->toEqual($formData['author_id']);
    expect($package->url)->toEqual($formData['url']);
    expect($package->abstract)->toEqual($formData['abstract']);
    expect($package->instructions)->toEqual($formData['instructions']);
    expect($package->composer_name)->toEqual("{$formData['packagist_namespace']}/{$formData['packagist_name']}");
    expect($package->repo_url)->toEqual($formData['url']);
    expect($package->readme_source)->toEqual($source);
    expect($package->readme)->toEqual($readme);
    expect($package->latest_version)->toEqual($version);
    expect($package->tags->contains('id', Tag::where('slug', 'new-tag')->first()->id))->toBeTrue();
    expect($package->tags->contains('id', $existingTag->id))->toBeTrue();
});

test('an authenticated user can view the edit package page', function () {
    [$packageA, $user] = createPackageWithUser();
    $screenshot = Screenshot::factory()->create(['uploader_id' => $user->id]);
    $packageA->screenshots()->save($screenshot);
    $packageB = Package::factory()->create();

    $response = $this->actingAs($user)->get(route('app.packages.edit', $packageA));

    $response->assertSuccessful();
    $response->assertViewHas('package');
    $response->assertViewHas('collaborators');
    $response->assertViewHas('tags');
    $response->assertViewHas('screenshots', function ($viewScreenshots) use ($screenshot) {
        return $viewScreenshots->count() === 1 && $viewScreenshots->first()['public_url'] == Storage::url($screenshot->path);
    });
    $response->assertDontSee('This URL was recently marked as inaccessible. Please review and update as necessary!');
});

test('package author can view disabled package form', function () {
    [$package, $author] = createPackageWithUser();

    $package->is_disabled = true;
    $package->save();

    $this->actingAs($author)->get(route('app.packages.edit', $package))
        ->assertSuccessful();

    $otherUser = User::factory()->create();
    $this->actingAs($otherUser)->get(route('app.packages.edit', $package))
        ->assertStatus(404);
});

test('if package is unavailable user sees notice on form', function () {
    [$package, $user] = createPackageWithUser();
    $package->update([
        'marked_as_unavailable_at' => now(),
    ]);
    $this->actingAs($user)->get(route('app.packages.edit', $package))
        ->assertSuccessful()
        ->assertSee('This URL was recently marked as inaccessible. Please review and update as necessary!');
});

test('the composer name must be unique', function () {
    $this->fakesRepoFromRequest();

    $existingPackage = Package::factory()->create(['composer_name' => 'tightenco/bae']);
    [$package, $user] = createPackageWithUser();

    $response = $this->actingAs($user)->put(route('app.packages.update', $package), [
        'packagist_namespace' => 'tightenco',
        'packagist_name' => 'bae',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('packagist_name');
});

test('the composer can remain the same', function () {
    $this->fakesRepoFromRequest();

    $package = Package::factory()->create(['composer_name' => 'tightenco/bae']);
    $collaborator = Collaborator::factory()->make();
    $user = User::factory()->create();
    $user->collaborators()->save($collaborator);
    $collaborator->authoredPackages()->save($package);

    $response = $this->actingAs($user)->put(route('app.packages.update', $package), array_merge($package->toArray(), [
        'packagist_namespace' => 'tightenco',
        'packagist_name' => 'bae',
    ]));

    $response->assertSessionHasNoErrors();
});

test('can update multiple screenshots', function () {
    $this->fakesRepoFromRequest();

    [$package, $user] = createPackageWithUser();
    [$oldScreenshot, $screenshotA, $screenshotB] = Screenshot::factory(3)->create(['uploader_id' => $user->id]);
    $package->screenshots()->save($oldScreenshot);

    $response = $this->actingAs($user)->put(route('app.packages.update', $package), array_merge(getValidPackageData(), [
        'screenshots' => [
            $screenshotA->id,
            $screenshotB->id,
        ],
    ]));

    tap($package->fresh()->screenshots, function ($packageScreenshots) use ($oldScreenshot, $screenshotA, $screenshotB) {
        expect($packageScreenshots)->toHaveCount(2);
        expect($packageScreenshots->contains($screenshotA))->toBeTrue();
        expect($packageScreenshots->contains($screenshotB))->toBeTrue();
        expect($packageScreenshots->contains($oldScreenshot))->toBeFalse();
    });

    $response->assertRedirect(route('app.packages.index'));
});

test('screenshots are optional', function () {
    $this->fakesRepoFromRequest();

    [$package, $user] = createPackageWithUser();

    $response = $this->actingAs($user)->put(route('app.packages.update', $package), getValidPackageData());

    expect($package->screenshots)->toHaveCount(0);
    $response->assertRedirect(route('app.packages.index'));
});

test('can not upload more than 20 screenshots', function () {
    [$package, $user] = createPackageWithUser();
    $screenshots = Screenshot::factory(21)->create(['uploader_id' => $user->id]);

    $response = $this->actingAs($user)->put(route('app.packages.update', $package), [
        'screenshots' => $screenshots->pluck('id'),
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('screenshots');
});

test('screenshots must be an array', function () {
    [$package, $user] = createPackageWithUser();

    $response = $this->actingAs($user)->put(route('app.packages.update', $package), [
        'screenshots' => 'not-an-array',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('screenshots');
});

test('all uploaded screenshots are returned when validation fails', function () {
    [$package, $user] = createPackageWithUser();
    [$oldScreenshot, $screenshotA, $screenshotB] = Screenshot::factory(3)->create(['uploader_id' => $user->id]);
    $package->screenshots()->save($oldScreenshot);

    $response = $this->actingAs($user)->put(route('app.packages.update', $package), [
        'packagist_namespace' => null,
        'screenshots' => [$oldScreenshot->id, $screenshotA->id, $screenshotB->id],
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('errors');
    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(['id' => $oldScreenshot->id, 'public_url' => Storage::url($oldScreenshot->path)], session('_old_input.screenshots')[0], ['id', 'public_url']);
    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(['id' => $screenshotA->id, 'public_url' => Storage::url($screenshotA->path)], session('_old_input.screenshots')[1], ['id', 'public_url']);
    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(['id' => $screenshotB->id, 'public_url' => Storage::url($screenshotB->path)], session('_old_input.screenshots')[2], ['id', 'public_url']);
});

test('the selected author is returned to the view when validation fails', function () {
    [$package, $user] = createPackageWithUser();
    $author = Collaborator::factory()->create();

    $response = $this->actingAs($user)->put(route('app.packages.update', $package), [
        'packagist_namespace' => null,
        'author_id' => $author->id,
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('errors');
    $this->assertNotNull(old('selectedAuthor'), 'Expected selectedAuthor is missing from the session');
    expect(old('selectedAuthor')->is($author))->toBeTrue();
});

test('the selected collaborators are returned to the view when validation fails', function () {
    [$package, $user] = createPackageWithUser();
    [$selectedCollaboratorA, $author, $selectedCollaboratorB] = Collaborator::factory(3)->create();
    $unselectedCollaborator = Collaborator::factory()->create();

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
        expect($sessionCollaborators)->toHaveCount(2);
        expect($sessionCollaborators->contains($selectedCollaboratorA))->toBeTrue();
        expect($sessionCollaborators->contains($selectedCollaboratorB))->toBeTrue();
        expect($sessionCollaborators->contains($unselectedCollaborator))->toBeFalse();
        $this->assertEquals(array_keys($sessionCollaborators->toArray()), range(0, count($sessionCollaborators) - 1), 'Failed asserting $sessionCollaborator keys are sequential integers');
    });
});

test('the selected existing tags and new tags are returned to the view when validation fails', function () {
    [$package, $user] = createPackageWithUser();
    $newTagName = 'New Tag';
    $selectedTags = collect([
        $tagA = Tag::factory()->create(['name' => 'Tag A']),
        $tagB = Tag::factory()->create(['name' => 'Tag B']),
        ['name' => $newTagName],
    ]);
    Tag::factory()->create(['name' => 'Excluded Tag']);

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
});

test('an existing tag is used if the tag submitted differs only in case', function () {
    $this->withoutExceptionHandling();
    $this->fakesRepoFromRequest();

    [$package, $user] = createPackageWithUser();
    $existingTagA = Tag::factory()->create(['name' => 'test tag a', 'slug' => 'test-tag-a']);
    $existingTagB = Tag::factory()->create(['name' => 'test tag b', 'slug' => 'test-tag-b']);

    $response = $this->actingAs($user)->put(route('app.packages.update', $package), [
        'name' => $this->faker->company(),
        'author_id' => $user->id,
        'url' => $this->faker->url(),
        'abstract' => $this->faker->sentence(),
        'instructions' => $this->faker->sentence(),
        'packagist_namespace' => $this->faker->word(),
        'packagist_name' => $this->faker->word(),
        'tags-new' => [
            'Test tag A',
            'Test tag B',
        ],
    ]);

    expect($package->tags)->toHaveCount(2);
    expect($package->tags->contains($existingTagA))->toBeTrue();
    expect($package->tags->contains($existingTagB))->toBeTrue();
    $response->assertRedirect(route('app.packages.index'));
});

test('an existing tag is used if the tag submitted differs only in case and a new tag is added', function () {
    $this->withoutExceptionHandling();
    $this->fakesRepoFromRequest();

    [$package, $user] = createPackageWithUser();
    $existingTag = Tag::factory()->create(['name' => 'test tag', 'slug' => 'test-tag']);

    $response = $this->actingAs($user)->put(route('app.packages.update', $package), [
        'name' => $this->faker->company(),
        'author_id' => $user->id,
        'url' => $this->faker->url(),
        'abstract' => $this->faker->sentence(),
        'instructions' => $this->faker->sentence(),
        'packagist_namespace' => $this->faker->word(),
        'packagist_name' => $this->faker->word(),
        'tags-new' => [
            'Test tag',
            'New Tag',
            'Another New Tag',
        ],
    ]);

    expect($package->tags)->toHaveCount(3);
    expect($package->tags->contains($existingTag))->toBeTrue();
    expect($package->tags->contains(Tag::where('name', 'new tag')->first()))->toBeTrue();
    expect($package->tags->contains(Tag::where('name', 'another new tag')->first()))->toBeTrue();
    $response->assertRedirect(route('app.packages.index'));
});

test('not updating url does not change package availability', function () {
    $this->withoutExceptionHandling();
    $this->fakesRepoFromRequest();

    [$package, $user] = createPackageWithUser();

    $package->marked_as_unavailable_at = now();
    $package->is_disabled = true;
    $package->save();

    $this->actingAs($user)->put(route('app.packages.update', $package), [
        'name' => $this->faker->company(),
        'author_id' => $user->id,
        'url' => $package->url,
        'abstract' => $this->faker->sentence(),
        'packagist_namespace' => $this->faker->word(),
        'packagist_name' => $this->faker->word(),
    ])
        ->assertRedirect(route('app.packages.index'));

    $this->assertNotNull($package->refresh()->marked_as_unavailable_at);
    expect($package->refresh()->is_disabled)->toBeTrue();
});

test('updating url attribute removes unavailable timestamp', function () {
    [$package, $user] = createPackageWithUser();

    $packagistNamespace = 'jedi';
    $packagistName = 'field-guide';

    $package->update(['url' => "https://github.com/{$packagistNamespace}/{$packagistName}"]);

    Http::fake([
        "https://github.com/{$packagistNamespace}/{$packagistName}.json" => Http::response(),
        "https://github.com/{$packagistNamespace}/{$packagistName}" => Http::response(),
        "https://packagist.org/packages/{$packagistNamespace}/{$packagistName}.json" => Http::response(),
    ]);

    $package->marked_as_unavailable_at = now();
    $package->is_disabled = true;
    $package->save();

    $this->actingAs($user)->put(route('app.packages.update', $package), [
        'name' => $this->faker->company(),
        'author_id' => $user->id,
        'url' => $this->faker->url(),
        'abstract' => $this->faker->sentence(),
        'packagist_namespace' => $packagistNamespace,
        'packagist_name' => $packagistName,
    ])
        ->assertRedirect(route('app.packages.index'));

    expect($package->refresh()->marked_as_unavailable_at)->toBeNull();
    expect($package->refresh()->is_disabled)->toBeFalse();
});

// Helpers
function getValidPackageData()
{
    return array_merge(Package::factory()->make()->toArray(), [
        'packagist_namespace' => 'tightenco',
        'packagist_name' => 'bae',
    ]);
}

function createPackageWithUser()
{
    $package = Package::factory()->make();
    $collaborator = Collaborator::factory()->make();
    $user = User::factory()->create();
    $user->collaborators()->save($collaborator);
    $collaborator->authoredPackages()->save($package);
    $package->tags()->save(Tag::factory()->create());

    return [$package, $user];
}
