<?php

use App\Models\Collaborator;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('name with username attribute', function () {
    $this->assertEquals(
        'Ted Lasso (tedlasso)',
        Collaborator::factory()->create([
            'name' => 'Ted Lasso',
            'github_username' => 'tedlasso',
        ])->name_with_username
    );

    $this->assertEquals(
        'Ted Lasso',
        Collaborator::factory()->create([
            'name' => 'Ted Lasso',
            'github_username' => null,
        ])->name_with_username
    );
});
