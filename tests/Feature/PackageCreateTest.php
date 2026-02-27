<?php

use App\Models\Collaborator;
use App\Models\Package;
use App\Models\Screenshot;
use App\Models\Tag;
use App\Models\User;
use App\ReadmeFormatter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;


test('can attach screenshots to the package', function () {
    Event::fake();

    $this->fakesRepoFromRequest();

    $user = User::factory()->create();
    [$screenshotA, $screenshotB] = Screenshot::factory(2)->create(['uploader_id' => $user->id]);
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
    expect($packageScreenshots)->toHaveCount(2);
    expect($packageScreenshots->contains($screenshotA))->toBeTrue();
    expect($packageScreenshots->contains($screenshotB))->toBeTrue();
    $response->assertRedirect(route('app.packages.index'));
});

test('screenshots are optional', function () {
    Event::fake();

    $this->fakesRepoFromRequest();

    $user = User::factory()->create();
    $validPackageData = array_merge($package = Package::factory()->make()->toArray(), [
        'packagist_namespace' => explode('/', $package['composer_name'])[0],
        'packagist_name' => explode('/', $package['composer_name'])[1],
    ]);

    $response = $this->actingAs($user)->post(route('app.packages.store'), $validPackageData);

    expect(Package::all())->toHaveCount(1);
    expect(Package::first()->screenshots)->toHaveCount(0);
    $response->assertRedirect(route('app.packages.index'));
});

test('screenshots must be an array', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('app.packages.store'), [
        'screenshots' => 'not-an-array',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('screenshots');
});

test('can not attach more than 20 screenshots', function () {
    $user = User::factory()->create();
    $screenshots = Screenshot::factory(21)->create(['uploader_id' => $user->id]);

    $response = $this->actingAs($user)->post(route('app.packages.store'), [
        'screenshots' => $screenshots->pluck('id'),
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('screenshots');
});

test('all uploaded screenshots are returned when validation fails', function () {
    $user = User::factory()->create();
    [$screenshotA, $screenshotB] = Screenshot::factory(2)->create(['uploader_id' => $user->id]);

    $response = $this->actingAs($user)->post(route('app.packages.store'), [
        'packagist_namespace' => null,
        'screenshots' => [
            $screenshotA->id,
            $screenshotB->id,
        ],
    ]);

    // dd(session('_old_input.screenshots')->all());
    $response->assertStatus(302);
    $response->assertSessionHas('errors');

    $sessionScreenshots = session('_old_input.screenshots')->all();

    $expectedScreenshots = [
        ['id' => $screenshotA->id, 'public_url' => Storage::url($screenshotA->path)],
        ['id' => $screenshotB->id, 'public_url' => Storage::url($screenshotB->path)],
    ];

    expect($sessionScreenshots)->toHaveCount(count($expectedScreenshots));

    expect($sessionScreenshots[0]['id'])->toEqual($screenshotA->id);
    expect($sessionScreenshots[0]['public_url'])->toEqual(Storage::url($screenshotA->path));
    expect($sessionScreenshots[1]['id'])->toEqual($screenshotB->id);
    expect($sessionScreenshots[1]['public_url'])->toEqual(Storage::url($screenshotB->path));
});

test('the selected author is returned to the view when validation fails', function () {
    Event::fake();

    $user = User::factory()->create();
    $author = Collaborator::factory()->create();

    $response = $this->actingAs($user)->post(route('app.packages.store'), [
        'packagist_namespace' => null,
        'author_id' => $author->id,
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('errors');
    $this->assertNotNull(old('selectedAuthor'), 'Expected selectedAuthor is missing from the session');
    expect(old('selectedAuthor')->is($author))->toBeTrue();
});

test('the selected collaborators are returned to the view when validation fails', function () {
    Event::fake();

    $user = User::factory()->create();
    [$selectedCollaboratorA, $author, $selectedCollaboratorB] = Collaborator::factory(3)->create();
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
        expect($sessionCollaborators)->toHaveCount(2);
        expect($sessionCollaborators->contains($selectedCollaboratorA))->toBeTrue();
        expect($sessionCollaborators->contains($selectedCollaboratorB))->toBeTrue();
        expect($sessionCollaborators->contains($unselectedCollaborator))->toBeFalse();
        $this->assertEquals(array_keys($sessionCollaborators->toArray()), range(0, count($sessionCollaborators) - 1), 'Failed asserting $sessionCollaborator keys are sequential integers');
    });
});

test('the selected existing tags and new tags are returned to the view when validation fails', function () {
    Event::fake();

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
});

test('relative urls are formatted to the latest release', function () {
    Event::fake();

    Http::fake([
        'https://packagist.org/packages/starwars/lightsabers.json' => Http::response([
            'package' => [
                'repository' => 'https://github.com/starwars/lightsabers',
                'versions' => [

                ],
            ],
        ]),
        'https://api.github.com/repos/starwars/lightsabers/readme' => Http::response('<div id="readme" class="md" data-path="README.md"><article class="markdown-body entry-content p-5" itemprop="text"><p>Finding a <a href="kyber-crystal">kyber crystal</a> for your lightsaber</p></article></div>'),
        'https://api.github.com/repos/starwars/lightsabers/releases' => Http::response([
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

    $package = Package::where('name', 'Lightsabers')->firstOrFail();
    $readme = (new ReadmeFormatter($package))->format($package->readme);

    // The github link should be based on the latest release tag rather than the release name
    $this->assertStringNotContainsStringIgnoringCase(
        'https://github.com/starwars/lightsabers/blob/Release/kyber-crystal',
        $readme
    );
    $this->assertStringContainsStringIgnoringCase(
        'https://github.com/starwars/lightsabers/blob/v1.0/kyber-crystal',
        $readme
    );
});

test('an existing tag is used if the tag submitted differs only in case', function () {
    $this->withoutExceptionHandling();
    Event::fake();
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

    expect(Tag::all())->toHaveCount(2);
    expect(Package::first()->tags->contains($existingTagA))->toBeTrue();
    expect(Package::first()->tags->contains($existingTagB))->toBeTrue();
    $response->assertRedirect(route('app.packages.index'));
});

test('an existing tag is used if the tag submitted differs only in case and a new tag is added', function () {
    $this->withoutExceptionHandling();
    Event::fake();
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

    expect(Tag::all())->toHaveCount(3);
    expect(Package::first()->tags->contains($existingTag))->toBeTrue();
    expect(Package::first()->tags->contains(Tag::where('name', 'new tag')->first()))->toBeTrue();
    expect(Package::first()->tags->contains(Tag::where('name', 'another new tag')->first()))->toBeTrue();
    $response->assertRedirect(route('app.packages.index'));
});
