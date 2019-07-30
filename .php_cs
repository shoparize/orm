<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->notPath('src/Symfony/Component/Translation/Tests/fixtures/resources.php')
    ->in(__DIR__ . "/src")
    ->in(__DIR__ . "/tests")
;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setHideProgress(false)
    ->setRules([
        '@PSR2' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
        '@PhpCsFixer' => true,
        '@PHP73Migration' => true,
        'no_php4_constructor' => true,
    ])
    ->setFinder($finder)
;
