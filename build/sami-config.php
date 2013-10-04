<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__ . '/../src')
;

return new Sami($iterator, array(
    'theme'                => 'enhanced',
//    'versions'             => '1.2',
    'title'                => 'Skajdo/EventManager',
//    'build_dir'            => __DIR__.'/../build/sf2/%version%',
//    'cache_dir'            => __DIR__.'/../cache/sf2/%version%',
    'build_dir'            => __DIR__ . '/api',
    'cache_dir'            => __DIR__ . '/cache',
    'default_opened_level' => 2,
//    'simulate_namespaces' => true,
));