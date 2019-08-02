<?php

$finder = PhpCsFixer\Finder::create();

if(file_exists(__DIR__ . "/src")){
    $finder->in(__DIR__ . "/src");
}

if(file_exists(__DIR__ . "/tests")){
    $finder->in(__DIR__ . "/tests");
}

if(file_exists(__DIR__ . "/vendor/benzine")){
    foreach(new DirectoryIterator(__DIR__ . "/vendor/benzine") as $file){
        if(!$file->isDot()){
            if($file->isDir()){
                if(file_exists($file->getRealPath() . "/src")) {
                    $finder->in($file->getRealPath() . "/src");
                }
                if(file_exists($file->getRealPath() . "/tests")) {
                    $finder->in($file->getRealPath() . "/tests");
                }
            }
        }
    }
}

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
