<?php
declare(strict_types=1);

/**
 * Test runner.
 *
 * Usage (from the plugin root):
 *   php tests/run.php
 *
 * Discovers every *Test.php file in the tests/ directory, instantiates the
 * class, calls run(), then prints a summary via report().
 */

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/TestCase.php';

$files = glob(__DIR__ . '/*Test.php') ?: [];
$total = 0;
$pass  = 0;
$fail  = 0;

foreach ($files as $file) {
    require_once $file;
    $class = basename($file, '.php');

    if (!class_exists($class)) {
        echo "SKIP: {$class} (class not found after require)\n";
        continue;
    }

    /** @var TestCase $t */
    $t = new $class();
    $t->run();
    $t->report();

    $total++;
}

echo "\nDone: {$total} test file(s)\n";
