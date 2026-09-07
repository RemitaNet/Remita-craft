<?php
declare(strict_types=1);

use remita\craftremitapayment\responses\PaymentResponse;
use craft\commerce\base\RequestResponseInterface;

class PaymentResponseTest extends TestCase
{
    public function run(): void
    {
        $this->testImplementsInterface();
        $this->testSuccessfulResponse();
        $this->testRedirectResponse();
        $this->testFailureResponse();
        $this->testGetCode();
        $this->testGetTransactionReference();
        $this->testGetRedirectUrl();
        $this->testGetMessage();
        $this->testGetData();
        $this->testIsProcessingWhenNotSuccessNotRedirect();
        $this->testGetRedirectMethod();
        $this->testGetRedirectData();
    }

    // -----------------------------------------------------------------------

    private function makeResponse(array $data, bool $success = false, bool $redirect = false): PaymentResponse
    {
        return new PaymentResponse($data, $success, $redirect);
    }

    private function testImplementsInterface(): void
    {
        $r = $this->makeResponse([]);
        $this->assertInstanceOf(
            RequestResponseInterface::class,
            $r,
            'PaymentResponse must implement RequestResponseInterface'
        );
    }

    private function testSuccessfulResponse(): void
    {
        $r = $this->makeResponse(['status' => '00'], true);
        $this->assertTrue($r->isSuccessful(), 'isSuccessful() must be true when $success=true');
        $this->assertFalse($r->isRedirect(),   'isRedirect() must be false when $redirect=false');
        $this->assertFalse($r->isProcessing(), 'isProcessing() must be false for a successful response');
    }

    private function testRedirectResponse(): void
    {
        $r = $this->makeResponse(['paymentLink' => 'https://pay.remita.net/checkout/abc'], false, true);
        $this->assertFalse($r->isSuccessful(), 'isSuccessful() must be false when $success=false');
        $this->assertTrue($r->isRedirect(),    'isRedirect() must be true when $redirect=true');
    }

    private function testFailureResponse(): void
    {
        $r = $this->makeResponse(['message' => 'Insufficient funds'], false, false);
        $this->assertFalse($r->isSuccessful(), 'isSuccessful() must be false on failure');
        $this->assertFalse($r->isRedirect(),   'isRedirect() must be false on failure');
        $this->assertTrue($r->isProcessing(),  'isProcessing() must be true when not success and not redirect');
    }

    private function testGetCode(): void
    {
        $r = $this->makeResponse(['status' => '00']);
        $this->assertSame('00', $r->getCode(), 'getCode() must return the status field as a string');

        $noStatus = $this->makeResponse([]);
        $this->assertSame('failed', $noStatus->getCode(), 'getCode() must fall back to "failed"');
    }

    private function testGetTransactionReference(): void
    {
        $r = $this->makeResponse(['paymentIdentifier' => 'ORD-abc123']);
        $this->assertSame('ORD-abc123', $r->getTransactionReference());

        $empty = $this->makeResponse([]);
        $this->assertSame('', $empty->getTransactionReference(), 'getTransactionReference() must return "" when not set');
    }

    private function testGetRedirectUrl(): void
    {
        $url = 'https://pay.remita.net/checkout/xyz';
        $r   = $this->makeResponse(['paymentLink' => $url]);
        $this->assertSame($url, $r->getRedirectUrl());

        $noLink = $this->makeResponse([]);
        $this->assertSame('', $noLink->getRedirectUrl(), 'getRedirectUrl() must return "" when paymentLink is absent');
    }

    private function testGetMessage(): void
    {
        $r = $this->makeResponse(['message' => 'Payment successful']);
        $this->assertSame('Payment successful', $r->getMessage());

        $empty = $this->makeResponse([]);
        $this->assertSame('', $empty->getMessage(), 'getMessage() must return "" when message is absent');
    }

    private function testGetData(): void
    {
        $data = ['status' => '00', 'paymentIdentifier' => 'ORD-999'];
        $r    = $this->makeResponse($data);
        $this->assertSame($data, $r->getData(), 'getData() must return the original data array');
    }

    private function testIsProcessingWhenNotSuccessNotRedirect(): void
    {
        $r = $this->makeResponse([], false, false);
        $this->assertTrue($r->isProcessing());
    }

    private function testGetRedirectMethod(): void
    {
        $r = $this->makeResponse([]);
        $this->assertSame('GET', $r->getRedirectMethod(), 'getRedirectMethod() must return "GET"');
    }

    private function testGetRedirectData(): void
    {
        $r = $this->makeResponse([]);
        $this->assertSame([], $r->getRedirectData(), 'getRedirectData() must return an empty array');
    }
}
