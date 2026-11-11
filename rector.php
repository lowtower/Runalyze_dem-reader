<?php

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\ClassMethod\AddReturnTypeDeclarationRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNativeCallRector;
use Rector\TypeDeclaration\Rector\Param\ParamTypeFromStrictNativeCallRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\CodingStyle\Rector\ClassMethod\ReturnArrayTypeFromArrayReturnRector;
use Rector\Php80\Rector\ClassMethod\AddTodoBasedOnNewPhp80FeaturesRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rules([
        // Typdeklarationen
        TypedPropertyFromAssignRector::class,
        AddReturnTypeDeclarationRector::class,
        ReturnTypeFromStrictNativeCallRector::class,
        ParamTypeFromStrictNativeCallRector::class,
        ReturnArrayTypeFromArrayReturnRector::class,
        
        // Code-Qualität
        RemoveUselessParamTagRector::class,
        CompleteDynamicPropertiesRector::class,
        
        // PHP 8.1+ Features
        ReadOnlyPropertyRector::class,
    ]);

    $rectorConfig->paths([
        __DIR__ . '/src',
    ]);

    $rectorConfig->phpVersion('8.5');
    $rectorConfig->parallel();
};
