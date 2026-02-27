<?php

use App\Jobs\SyncPackagePackagistData;
use PHPUnit\Framework\TestCase;

it('parses nova version from composer constraint', function (?string $constraint, ?int $expected) {
    expect(SyncPackagePackagistData::parseNovaVersion($constraint))->toBe($expected);
})->with('novaVersionConstraints');

// Datasets
dataset('novaVersionConstraints', [
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
]);
