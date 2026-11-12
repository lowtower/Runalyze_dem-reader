<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddReturnTypeDeclarationRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withRootFiles()
    ->withIndent(indentChar: ' ', indentSize: 4)
    ->withTreatClassesAsFinal()
    ->withSets([
        LevelSetList::UP_TO_PHP_85,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
    ])
    ->withTypeCoverageLevel(67)         # max level 67
    ->withTypeCoverageDocblockLevel(17) # max level 17
    ->withDeadCodeLevel(72)             # max level 72
    ->withCodeQualityLevel(67)          # max level 67
    ->withCodingStyleLevel(21)          # max level 21
    ->withRules([
        // Typdeklarationen
        AddReturnTypeDeclarationRector::class,
    ])
    ->withSkip([])
    ->withComposerBased(
        true,   // symfony
        true,   // doctrine
        true,   // phpunit
        true,   // twig
    )
    ->withAttributesSets()
    ->withSets([])
;
