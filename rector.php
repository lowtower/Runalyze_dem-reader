<?php

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\ClassMethod\AddReturnTypeDeclarationRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNativeCallRector;
use Rector\TypeDeclaration\Rector\Param\ParamTypeFromStrictNativeCallRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\Php80\Rector\ClassMethod\AddTodoBasedOnNewPhp80FeaturesRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rules([
        // Typdeklarationen
        TypedPropertyFromAssignRector::class,
        AddReturnTypeDeclarationRector::class,
        ReturnTypeFromStrictNativeCallRector::class,
        ParamTypeFromStrictNativeCallRector::class,
        
        // Code-Qualität
        RemoveUselessParamTagRector::class,
    ]);

    $rectorConfig->paths([
        __DIR__ . '/src',
    ]);

    $rectorConfig->phpVersion('8.5');
};
