<?php

namespace Src\Common\Tests\Adapters;

require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\TextUI\TestRunner;

$phpunit = new TestRunner();

try {
    $suit = $phpunit->getTest(__DIR__ . '/ValidatorTest.php');
    $test_results = $phpunit->run($suit, ['extensions' => []]);
} catch (\Throwable $e) {
    print $e->getMessage() . "\n";
    die ("Unit tests failed.");
}