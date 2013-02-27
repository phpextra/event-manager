<?php

/**
 * Main executable
 */

include(__DIR__ . '/../vendor/autoload.php');
use Skajdo\TestSuite\TestSuite;

if(PHP_SAPI != 'cli'){
    TestSuite::createEngine()->discoverTests(__DIR__)->runTests();
}else{
    TestSuite::createConsoleEngine()->run();
}

