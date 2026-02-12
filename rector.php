<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPublicMethodParameterRector;
use Rector\Privatization\Rector\ClassMethod\PrivatizeFinalClassMethodRector;
use Rector\Privatization\Rector\MethodCall\PrivatizeLocalGetterToPropertyRector;
use Rector\Privatization\Rector\Property\PrivatizeFinalClassPropertyRector;
use Rector\TypeDeclaration\Rector\ArrowFunction\AddArrowFunctionReturnTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictTypedCallRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnUnionTypeRector;
use Rector\TypeDeclaration\Rector\Closure\AddClosureVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;
use RectorLaravel\Set\LaravelSetProvider;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/bootstrap',
        __DIR__.'/config',
        __DIR__.'/public',
        __DIR__.'/resources',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ])
    ->withSkip([
        // Skip directories
        __DIR__.'/bootstrap/cache',
        __DIR__.'/storage',
        __DIR__.'/vendor',

        // Skip globally - these don't work well with Livewire/Volt patterns
        AddClosureVoidReturnTypeWhereNoReturnRector::class,
        ReturnTypeFromStrictTypedCallRector::class,
        ReturnUnionTypeRector::class,
        AddArrowFunctionReturnTypeRector::class,

        // Skip strict types for blade views and Pest test files
        DeclareStrictTypesRector::class => [
            __DIR__.'/resources/views/**/*.blade.php',
            __DIR__.'/resources/views/pages/**/*.test.php',
        ],

        // Livewire Volt pages use anonymous classes - skip privatization rules
        PrivatizeFinalClassMethodRector::class => [
            __DIR__.'/resources/views/pages',
        ],
        PrivatizeFinalClassPropertyRector::class => [
            __DIR__.'/resources/views/pages',
        ],
        PrivatizeLocalGetterToPropertyRector::class => [
            __DIR__.'/resources/views/pages',
        ],
        RemoveUnusedPublicMethodParameterRector::class => [
            __DIR__.'/resources/views/pages',
        ],
    ])
    ->withPhpSets()
    ->withSetProviders(LaravelSetProvider::class)
    ->withImportNames()
    ->withComposerBased(laravel: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        earlyReturn: true,
    )
    ->withRules([
        DeclareStrictTypesRector::class,
    ]);
