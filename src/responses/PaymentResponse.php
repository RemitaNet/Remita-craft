<?php
declare(strict_types=1);

namespace remita\craftremitapayment\responses;

use craft\commerce\base\RequestResponseInterface;

class PaymentResponse implements RequestResponseInterface
{
    protected array $data;
    protected bool $success;
    protected bool $redirect;

    public function __construct(array $data, bool $success = false, bool $redirect = false)
    {
        $this->data = $data;
        $this->success = $success;
        $this->redirect = $redirect;
    }

    public function isSuccessful(): bool { return $this->success; }
    public function isProcessing(): bool { return !$this->success && !$this->redirect; }
    public function isRedirect(): bool { return $this->redirect; }
    public function getRedirectUrl(): string { return $this->data['paymentLink'] ?? ''; }
    public function getRedirectMethod(): string { return 'GET'; }
    public function getRedirectData(): array { return []; }
    public function getMessage(): string { return $this->data['message'] ?? ''; }
    public function getCode(): string { return (string)($this->data['status'] ?? 'failed'); }
    public function getData(): array { return $this->data; }
    public function getTransactionReference(): string { return $this->data['paymentIdentifier'] ?? ''; }
}
