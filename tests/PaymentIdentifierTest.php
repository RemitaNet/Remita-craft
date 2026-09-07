<?php
declare(strict_types=1);

use remita\craftremitapayment\helpers\PaymentIdentifier;

class PaymentIdentifierTest extends TestCase
{
    public function run(): void
    {
        $this->testGenerateReturnsNonEmptyString();
        $this->testGenerateHasDefaultPrefix();
        $this->testGenerateHasCustomPrefix();
        $this->testGeneratedValueIsUnique();
        $this->testIsValidReturnsTrueForTypicalIdentifier();
        $this->testIsValidReturnsFalseForEmptyString();
        $this->testIsValidReturnsFalseForStringWithSpace();
        $this->testIsValidReturnsFalseForStringWithTab();
        $this->testIsValidReturnsFalseForStringWithNewline();
    }

    // -----------------------------------------------------------------------

    private function testGenerateReturnsNonEmptyString(): void
    {
        $id = PaymentIdentifier::generate();
        $this->assertTrue($id !== '', 'generate() must return a non-empty string');
    }

    private function testGenerateHasDefaultPrefix(): void
    {
        $id = PaymentIdentifier::generate();
        $this->assertTrue(
            str_starts_with($id, 'ORD-'),
            'generate() default result must start with "ORD-"'
        );
    }

    private function testGenerateHasCustomPrefix(): void
    {
        $id = PaymentIdentifier::generate('TXN-');
        $this->assertTrue(
            str_starts_with($id, 'TXN-'),
            'generate("TXN-") must start with "TXN-"'
        );
    }

    private function testGeneratedValueIsUnique(): void
    {
        $a = PaymentIdentifier::generate();
        $b = PaymentIdentifier::generate();
        $this->assertFalse($a === $b, 'Two successive generate() calls must differ');
    }

    private function testIsValidReturnsTrueForTypicalIdentifier(): void
    {
        $id = PaymentIdentifier::generate();
        $this->assertTrue(
            PaymentIdentifier::isValid($id),
            'isValid() must return true for a freshly generated identifier'
        );
    }

    private function testIsValidReturnsFalseForEmptyString(): void
    {
        $this->assertFalse(
            PaymentIdentifier::isValid(''),
            'isValid("") must return false'
        );
    }

    private function testIsValidReturnsFalseForStringWithSpace(): void
    {
        $this->assertFalse(
            PaymentIdentifier::isValid('ORD- 123'),
            'isValid() must return false when identifier contains a space'
        );
    }

    private function testIsValidReturnsFalseForStringWithTab(): void
    {
        $this->assertFalse(
            PaymentIdentifier::isValid("ORD-\t123"),
            'isValid() must return false when identifier contains a tab'
        );
    }

    private function testIsValidReturnsFalseForStringWithNewline(): void
    {
        $this->assertFalse(
            PaymentIdentifier::isValid("ORD-\n123"),
            'isValid() must return false when identifier contains a newline'
        );
    }
}
