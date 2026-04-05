<?php

/**
 * @noinspection DevelopmentDependenciesUsageInspection
 * @noinspection PhpUnhandledExceptionInspection
 * @noinspection TransitiveDependenciesUsageInspection
 */

declare(strict_types=1);

use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withRootFiles()

    ->withPhpCsFixerSets(
        perCS30: true,
    )

    ->withPreparedSets(
        arrays: true,
        spaces: true,
        namespaces: true,
        cleanCode: true,
    )

    ->withRules([
    ])

    ->withSkip([
        NotOperatorWithSuccessorSpaceFixer::class,
    ]);
