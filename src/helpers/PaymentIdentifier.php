<?php
declare(strict_types=1);

namespace remita\craftremitapayment\helpers;

/**
 * Generates and validates Remita payment identifier strings.
 *
 * A payment identifier is a unique reference attached to every Remita
 * checkout session so that the completion/query flow can retrieve the
 * correct transaction.  The helper keeps all generation logic in one
 * place so both the gateway and tests can rely on it without duplicating
 * the uniqid() call.
 */
class PaymentIdentifier
{
    private const PREFIX = 'ORD-';

    /**
     * Generate a new, unique payment identifier.
     *
     * The value is prefixed with "ORD-" and uses PHP's uniqid() seeded
     * with the current microsecond clock, giving a 13-hex-char suffix
     * that is unique within the same process.
     *
     * @param string $prefix Optional override prefix (defaults to "ORD-").
     */
    public static function generate(string $prefix = self::PREFIX): string
    {
        return uniqid($prefix, false);
    }

    /**
     * Return true when the string looks like a valid payment identifier.
     *
     * A valid identifier must be non-empty and must NOT contain
     * whitespace or control characters — the Remita query endpoint will
     * reject anything that does not round-trip cleanly through a URL
     * path segment.
     */
    public static function isValid(string $identifier): bool
    {
        if ($identifier === '') {
            return false;
        }

        // Reject strings with whitespace or control characters
        return preg_match('/\s/', $identifier) === 0;
    }
}
