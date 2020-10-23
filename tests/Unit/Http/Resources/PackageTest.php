<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\Package as PackageResource;
use App\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PackageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_abstract_is_returned_if_the_resource_has_an_abstract()
    {
        $abstract = 'This is the test abstract';
        $package = Package::factory()->create([
            'abstract' => $abstract,
            'description' => 'This is the test description',
        ]);

        $packageResource = (new PackageResource($package))->jsonSerialize();

        $this->assertEquals($abstract, $packageResource['abstract']);
    }

    /** @test */
    public function an_abstractified_value_is_returned_when_the_abstract_is_null()
    {
        $package = Package::factory()->create([
            'abstract' => null,
        ]);

        $packageResource = (new PackageResource($package))->jsonSerialize();

        $this->assertNotNull($packageResource['abstract']);
        $this->assertEquals($packageResource['abstract'], $package->abstract);
    }
}
