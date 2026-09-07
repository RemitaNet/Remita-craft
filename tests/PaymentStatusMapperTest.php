<?php
declare(strict_types=1);

use remita\craftremitapayment\helpers\PaymentStatusMapper;

class PaymentStatusMapperTest extends TestCase
{
    public function run(): void
    {
        $this->testIsSuccessReturnsTrueForCode00();
        $this->testIsSuccessReturnsFalseForOtherCodes();
        $this->testIsPendingReturnsTrueForCode01();
        $this->testIsPendingReturnsTrueForCode021();
        $this->testIsPendingReturnsFalseForSuccessCode();
        $this->testIsPendingReturnsFalseForFailedCode();
        $this->testLabelForSuccess();
        $this->testLabelForPending();
        $this->testLabelForInProgress();
        $this->testLabelForFailed();
        $this->testLabelForUnknownCode();
        $this->testConstantValues();
    }

    // -----------------------------------------------------------------------

    private function testIsSuccessReturnsTrueForCode00(): void
    {
        $this->assertTrue(
            PaymentStatusMapper::isSuccess('00'),
            'isSuccess("00") must be true'
        );
    }

    private function testIsSuccessReturnsFalseForOtherCodes(): void
    {
        foreach (['01', '021', '025', '', 'XX'] as $code) {
            $this->assertFalse(
                PaymentStatusMapper::isSuccess($code),
                "isSuccess(\"{$code}\") must be false"
            );
        }
    }

    private function testIsPendingReturnsTrueForCode01(): void
    {
        $this->assertTrue(
            PaymentStatusMapper::isPending('01'),
            'isPending("01") must be true'
        );
    }

    private function testIsPendingReturnsTrueForCode021(): void
    {
        $this->assertTrue(
            PaymentStatusMapper::isPending('021'),
            'isPending("021") must be true'
        );
    }

    private function testIsPendingReturnsFalseForSuccessCode(): void
    {
        $this->assertFalse(
            PaymentStatusMapper::isPending('00'),
            'isPending("00") must be false'
        );
    }

    private function testIsPendingReturnsFalseForFailedCode(): void
    {
        $this->assertFalse(
            PaymentStatusMapper::isPending('025'),
            'isPending("025") must be false'
        );
    }

    private function testLabelForSuccess(): void
    {
        $this->assertSame(
            'Successful',
            PaymentStatusMapper::label('00'),
            'label("00") must be "Successful"'
        );
    }

    private function testLabelForPending(): void
    {
        $this->assertSame(
            'Pending',
            PaymentStatusMapper::label('01'),
            'label("01") must be "Pending"'
        );
    }

    private function testLabelForInProgress(): void
    {
        $this->assertSame(
            'In Progress',
            PaymentStatusMapper::label('021'),
            'label("021") must be "In Progress"'
        );
    }

    private function testLabelForFailed(): void
    {
        $this->assertSame(
            'Failed',
            PaymentStatusMapper::label('025'),
            'label("025") must be "Failed"'
        );
    }

    private function testLabelForUnknownCode(): void
    {
        $this->assertSame(
            'Unknown',
            PaymentStatusMapper::label('999'),
            'label("999") must be "Unknown" for an unrecognised code'
        );
    }

    private function testConstantValues(): void
    {
        $this->assertSame('00',  PaymentStatusMapper::SUCCESS,     'SUCCESS constant must be "00"');
        $this->assertSame('01',  PaymentStatusMapper::PENDING,     'PENDING constant must be "01"');
        $this->assertSame('021', PaymentStatusMapper::IN_PROGRESS, 'IN_PROGRESS constant must be "021"');
        $this->assertSame('025', PaymentStatusMapper::FAILED,      'FAILED constant must be "025"');
    }
}
