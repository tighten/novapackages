<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\Package as PackageResource;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PackageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_abstract_is_returned_if_the_resource_has_an_abstract(): void
    {
        $abstract = 'This is the test abstract';
        $package = Package::factory()->create([
            'abstract' => $abstract,
            'description' => 'This is the test description',
        ]);

        $packageResource = (new PackageResource($package))->jsonSerialize();

        $this->assertEquals($abstract, $packageResource['abstract']);
    }

    #[Test]
    public function an_abstractified_value_is_returned_when_the_abstract_is_null(): void
    {
        $package = Package::factory()->create([
            'abstract' => null,
        ]);

        $packageResource = (new PackageResource($package))->jsonSerialize();

        $this->assertNotNull($packageResource['abstract']);
        $this->assertEquals($packageResource['abstract'], $package->abstract);
    }
}
