<?php

use App\Http\Resources\PackageResource;
use App\Models\Collaborator;
use App\Models\Package;
use App\Models\User;

test('package abstracts default to truncated readme if no abstract', function () {
    $user = User::factory()->create();
    $collaborator = Collaborator::factory()->create();
    $user->collaborators()->save($collaborator);

    $package = $collaborator->authoredPackages()->save(Package::factory()->make([
        'abstract' => '',
        'readme' => 'Abcdef8181',
    ]));

    $response = $this->get(route('home'));

    $response->assertSee('Abcdef8181');

    // Let's check the resource, too; that check above is shaky
    $resource = PackageResource::from($package);

    expect($resource['abstract'])->toEqual('Abcdef8181');
});

test('long package readmes are truncated to 190 characters for abstract', function () {
    $user = User::factory()->create();
    $collaborator = Collaborator::factory()->create();
    $user->collaborators()->save($collaborator);

    $package = $collaborator->authoredPackages()->save(Package::factory()->make([
        'abstract' => '',
        'readme' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam lorem erat, luctus at diam sed, dapibus facilisis purus. In laoreet enim nunc, ut pretium arcu scelerisque in. Nunc eu cursus nibh.', // 195 characters
    ]));

    $resource = PackageResource::from($package);

    expect($resource['abstract'])->toContain('Lorem ipsum');
    $this->assertStringNotContainsString('nibh', $resource['abstract']);
});

test('api abstract never null', function () {
    $user = User::factory()->create();
    $collaborator = Collaborator::factory()->create();
    $user->collaborators()->save($collaborator);

    $package = $collaborator->authoredPackages()->save(Package::factory()->make([
        'abstract' => '',
    ]));

    $response = $this->get('api/recent');

    $this->assertNotEmpty($response->json()['data'][0]['abstract']);
});
