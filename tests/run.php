<?php

/**
 * Main executable
 */

date_default_timezone_set('Europe/Warsaw');
include(__DIR__ . '/../vendor/autoload.php');
use Skajdo\TestSuite\TestSuite;

if(PHP_SAPI != 'cli'){

    $engine = TestSuite::createEngine();

    if($engine->isCodeCoverageEnabled()){
        $filter = $engine->getCodeCoverageAnalyzer()->getCodeCoverageEngine()->filter();
        $filter->addDirectoryToWhitelist(realpath(__DIR__ . '/../src'));
    }

    $renderer = new Skajdo\TestSuite\Renderer\Html();
    $renderer->addWriter(new Skajdo\TestSuite\Writer\Screen());
    $renderer->addWriter(new Skajdo\TestSuite\Writer\File(__DIR__ . '/reports/report.html'));
    $engine->addRenderer($renderer);

    $renderer = new Skajdo\TestSuite\Renderer\Tap();
    $renderer->addWriter(new Skajdo\TestSuite\Writer\File(__DIR__ . '/reports/report.tap'));
    $engine->addRenderer($renderer);

    $renderer = new Skajdo\TestSuite\Renderer\Junit();
    $renderer->addWriter(new Skajdo\TestSuite\Writer\File(__DIR__ . '/reports/report.xml'));
    $engine->addRenderer($renderer);

    $renderer = new Skajdo\TestSuite\Renderer\Cli();
    $renderer->addWriter(new Skajdo\TestSuite\Writer\File(__DIR__ . '/reports/report.cli.txt'));
    $engine->addRenderer($renderer);

    $engine->discoverTests(__DIR__ . '/fixtures')->runTests();

    if($engine->isCodeCoverageEnabled()){
        $coverageHtml = new PHP_CodeCoverage_Report_HTML();
        $coverageHtml->process($engine->getCodeCoverageAnalyzer()->getCodeCoverageEngine(), __DIR__ . '/reports/coverage');

        $coverageClover = new PHP_CodeCoverage_Report_Clover();
        $coverageClover->process($engine->getCodeCoverageAnalyzer()->getCodeCoverageEngine(), __DIR__ . '/reports/clover.xml');
    }

}else{
    TestSuite::createConsoleEngine()->run();
}

