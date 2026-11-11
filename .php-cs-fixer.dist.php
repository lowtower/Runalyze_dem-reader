<?php

$header = <<<EOF
This file is part of the Runalyze DEM Reader.

(c) RUNALYZE <mail@runalyze.com>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        'header_comment' => [
            'header' => $header
        ],
        'ordered_imports' => true,
        'php_unit_construct' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'phpdoc_separation' => false,
        'type_declaration_spaces' => true,
        'single_type_element_per_declaration' => true,
        'array_syntax' => ['syntax' => 'short'],
        'list_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'fully_qualified_strict_types' => true,
        'phpdoc_add_missing_param_annotation' => false,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__.DIRECTORY_SEPARATOR.'src')
            ->exclude('Tests')
    )
;
