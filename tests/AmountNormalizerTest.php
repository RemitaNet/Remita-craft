<?php
declare(strict_types=1);

use remita\craftremitapayment\helpers\AmountNormalizer;

class AmountNormalizerTest extends TestCase
{
    public function run(): void
    {
        $this->testToKoboTypicalAmount();
        $this->testToKoboRoundsHalfUp();
        $this->testToKoboZeroAmount();
        $this->testToKoboLargeAmount();
        $this->testToKoboThrowsOnNegative();
        $this->testFromKoboTypicalValue();
        $this->testFromKoboZero();
        $this->testFromKoboThrowsOnNegative();
        $this->testRoundTrip();
    }

    // -----------------------------------------------------------------------

    private function testToKoboTypicalAmount(): void
    {
        $this->assertSame(
            150000,
            AmountNormalizer::toKobo(1500.00),
            'toKobo(1500.00) must equal 150000'
        );
    }

    private function testToKoboRoundsHalfUp(): void
    {
        // 1500.005 * 100 = 150000.5 — PHP round() uses "round half away from zero"
        $result = AmountNormalizer::toKobo(1500.005);
        $this->assertTrue(
            in_array($result, [150000, 150001], true),
            'toKobo(1500.005) must be 150000 or 150001 (rounding boundary)'
        );
    }

    private function testToKoboZeroAmount(): void
    {
        $this->assertSame(
            0,
            AmountNormalizer::toKobo(0.0),
            'toKobo(0.0) must equal 0'
        );
    }

    private function testToKoboLargeAmount(): void
    {
        $this->assertSame(
            10_000_000,
            AmountNormalizer::toKobo(100_000.00),
            'toKobo(100000.00) must equal 10000000'
        );
    }

    private function testToKoboThrowsOnNegative(): void
    {
        $this->expectException(
            \InvalidArgumentException::class,
            fn() => AmountNormalizer::toKobo(-1.0),
            'toKobo() must throw InvalidArgumentException for negative amounts'
        );
    }

    private function testFromKoboTypicalValue(): void
    {
        $this->assertSame(
            1500.00,
            AmountNormalizer::fromKobo(150000),
            'fromKobo(150000) must equal 1500.00'
        );
    }

    private function testFromKoboZero(): void
    {
        $this->assertSame(
            0.0,
            AmountNormalizer::fromKobo(0),
            'fromKobo(0) must equal 0.0'
        );
    }

    private function testFromKoboThrowsOnNegative(): void
    {
        $this->expectException(
            \InvalidArgumentException::class,
            fn() => AmountNormalizer::fromKobo(-100),
            'fromKobo() must throw InvalidArgumentException for negative kobo values'
        );
    }

    private function testRoundTrip(): void
    {
        $original = 2499.99;
        $kobo     = AmountNormalizer::toKobo($original);
        $result   = AmountNormalizer::fromKobo($kobo);

        $this->assertSame(
            $original,
            $result,
            "Round-trip {$original} -> kobo -> float must equal the original"
        );
    }
}
