<?php

namespace Tests\Unit;

use App\Collaborator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollaboratorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function name_with_username_attribute()
    {
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
    }
}
