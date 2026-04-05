<?php

/**
 * @noinspection DevelopmentDependenciesUsageInspection
 * @noinspection PhpUnhandledExceptionInspection
 * @noinspection TransitiveDependenciesUsageInspection
 */

declare(strict_types=1);

use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitSelfCallRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])

    ->withPhpSets(php85: true)

    ->withComposerBased(
        phpunit: true,
    )

    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
        phpunitCodeQuality: true,
    )

    ->withRules([
        PreferPHPUnitSelfCallRector::class,
    ])

    ->withSkip([
        EncapsedStringsToSprintfRector::class,
        PreferPHPUnitThisCallRector::class, // phpunitCodeQuality Set
    ]);
