<?php
declare(strict_types=1);

/**
 * Minimal test case base class.
 *
 * Provides lightweight assertion helpers that mirror the PHPUnit API so
 * individual test files are easy to read, and that print a PASS/FAIL
 * summary line at the end of each test class.
 */
abstract class TestCase
{
    private int $assertions = 0;
    private int $failures   = 0;

    /** Override in each concrete test class and call assertion helpers. */
    abstract public function run(): void;

    // -----------------------------------------------------------------------
    // Assertion helpers
    // -----------------------------------------------------------------------

    protected function assertSame(mixed $expected, mixed $actual, string $msg = ''): void
    {
        $this->assertions++;
        if ($expected !== $actual) {
            $this->failures++;
            $label = $msg ?: 'assertSame';
            echo "FAIL: {$label} — expected=" . var_export($expected, true)
                . ' got=' . var_export($actual, true) . "\n";
        }
    }

    protected function assertNull(mixed $v, string $m = ''): void
    {
        $this->assertSame(null, $v, $m ?: 'assertNull');
    }

    protected function assertTrue(mixed $v, string $m = ''): void
    {
        $this->assertSame(true, $v, $m ?: 'assertTrue');
    }

    protected function assertFalse(mixed $v, string $m = ''): void
    {
        $this->assertSame(false, $v, $m ?: 'assertFalse');
    }

    protected function assertNotNull(mixed $v, string $m = ''): void
    {
        $this->assertions++;
        if ($v === null) {
            $this->failures++;
            echo "FAIL: " . ($m ?: 'assertNotNull') . "\n";
        }
    }

    protected function assertGreaterThan(int|float $limit, int|float $actual, string $m = ''): void
    {
        $this->assertions++;
        if ($actual <= $limit) {
            $this->failures++;
            echo "FAIL: " . ($m ?: 'assertGreaterThan')
                . " — expected > {$limit}, got {$actual}\n";
        }
    }

    protected function assertInstanceOf(string $expected, mixed $actual, string $m = ''): void
    {
        $this->assertions++;
        if (!($actual instanceof $expected)) {
            $this->failures++;
            $got = is_object($actual) ? get_class($actual) : gettype($actual);
            echo "FAIL: " . ($m ?: 'assertInstanceOf')
                . " — expected {$expected}, got {$got}\n";
        }
    }

    protected function expectException(string $exceptionClass, callable $fn, string $m = ''): void
    {
        $this->assertions++;
        $caught = false;
        try {
            $fn();
        } catch (\Throwable $e) {
            if ($e instanceof $exceptionClass) {
                $caught = true;
            }
        }
        if (!$caught) {
            $this->failures++;
            echo "FAIL: " . ($m ?: 'expectException')
                . " — expected {$exceptionClass} to be thrown\n";
        }
    }

    // -----------------------------------------------------------------------
    // Reporting
    // -----------------------------------------------------------------------

    public function report(): void
    {
        $status = $this->failures === 0 ? 'PASS' : 'FAIL';
        printf(
            "[%s] %s — %d assertions, %d failures\n",
            $status,
            static::class,
            $this->assertions,
            $this->failures
        );
    }
}
