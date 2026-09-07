<?php
declare(strict_types=1);

namespace remita\craftremitapayment\helpers;

/**
 * Maps Remita API status codes to human-readable labels and success flags.
 *
 * Remita returns numeric string codes in the "status" field of every API
 * response.  This class centralises the mapping so the gateway and
 * PaymentResponse do not need to hard-code magic strings.
 *
 * Known codes:
 *   "00"  – Transaction successful
 *   "01"  – Transaction pending
 *   "021" – Transaction in progress / awaiting OTP
 *   "025" – Transaction failed
 */
class PaymentStatusMapper
{
    /** Status code for a completed, successful transaction. */
    public const SUCCESS = '00';

    /** Status code for a pending transaction. */
    public const PENDING = '01';

    /** Status code for a transaction awaiting further user action. */
    public const IN_PROGRESS = '021';

    /** Status code for a failed transaction. */
    public const FAILED = '025';

    private static array $labels = [
        self::SUCCESS     => 'Successful',
        self::PENDING     => 'Pending',
        self::IN_PROGRESS => 'In Progress',
        self::FAILED      => 'Failed',
    ];

    /**
     * Return true when the code represents a completed, successful payment.
     */
    public static function isSuccess(string $code): bool
    {
        return $code === self::SUCCESS;
    }

    /**
     * Return true when the code represents a payment that is still
     * processing and has not yet reached a terminal state.
     */
    public static function isPending(string $code): bool
    {
        return in_array($code, [self::PENDING, self::IN_PROGRESS], true);
    }

    /**
     * Return a human-readable label for the given status code.
     *
     * Returns "Unknown" for any code not in the known set.
     */
    public static function label(string $code): string
    {
        return self::$labels[$code] ?? 'Unknown';
    }
}
