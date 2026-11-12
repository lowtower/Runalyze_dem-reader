<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    // include *.php files in the root directory
    ->withRootFiles()
    // add a single rule
    ->withRules([
        NoUnusedImportsFixer::class,
    ])
    ->withPreparedSets(
        common: true,
        psr12: true,
        perCs: true,
        cleanCode: true
    )
    ->withSets([
        SetList::PSR_12,
        SetList::DOCTRINE_ANNOTATIONS,
    ])
    ->withSkip([])
;
