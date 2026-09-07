<?php
declare(strict_types=1);

namespace remita\craftremitapayment\helpers;

/**
 * Converts monetary amounts between Craft Commerce's float representation
 * and the integer-kobo (or lowest-denomination) value expected by the
 * Remita API.
 *
 * Craft Commerce stores amounts as floats (e.g. 1500.00 for ₦1,500).
 * Remita expects those amounts multiplied by 100 and rounded to the
 * nearest integer (e.g. 150000).  This class provides both directions
 * of the conversion and a guard against negative or zero values.
 */
class AmountNormalizer
{
    /**
     * Convert a Craft Commerce float amount to the integer kobo value
     * required by Remita.
     *
     * @throws \InvalidArgumentException when $amount is negative.
     */
    public static function toKobo(float $amount): int
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException(
                'Amount must be zero or positive, got: ' . $amount
            );
        }

        return (int) round($amount * 100);
    }

    /**
     * Convert a Remita kobo integer back to a Craft Commerce float.
     *
     * @throws \InvalidArgumentException when $kobo is negative.
     */
    public static function fromKobo(int $kobo): float
    {
        if ($kobo < 0) {
            throw new \InvalidArgumentException(
                'Kobo value must be zero or positive, got: ' . $kobo
            );
        }

        return round($kobo / 100, 2);
    }
}
