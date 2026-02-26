<?php

namespace Tests\Unit\Jobs;

use App\Jobs\SyncPackagePackagistData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SyncPackagePackagistDataTest extends TestCase
{
    #[Test]
    #[DataProvider('novaVersionConstraints')]
    public function it_parses_nova_version_from_composer_constraint(?string $constraint, ?int $expected): void
    {
        $this->assertSame($expected, SyncPackagePackagistData::parseNovaVersion($constraint));
    }

    public static function novaVersionConstraints(): array
    {
        return [
            'single caret constraint' => ['^4.0', 4],
            'single caret v5' => ['^5.0', 5],
            'multiple caret constraints picks highest' => ['^4.0|^5.0', 5],
            'multiple constraints reversed order' => ['^5.0|^4.0', 5],
            'tilde constraint' => ['~4.0', 4],
            'exact version' => ['4.0.0', 4],
            'greater than or equal' => ['>=4.0', 4],
            'three version constraints' => ['^3.0|^4.0|^5.0', 5],
            'constraint with spaces around pipe' => ['^4.0 | ^5.0', 5],
            'minor version differences' => ['^4.12', 4],
            'double pipe separator' => ['^4.0 || ^5.0', 5],
            'wildcard constraint' => ['4.*', 4],
            'null constraint' => [null, null],
            'empty string' => ['', null],
            'dev constraint' => ['dev-main', null],
        ];
    }
}
