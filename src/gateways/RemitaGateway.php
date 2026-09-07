<?php
declare(strict_types=1);

namespace remita\craftremitapayment\gateways;

use Craft;
use craft\commerce\base\Gateway as BaseGateway;
use craft\commerce\models\Transaction;
use craft\commerce\models\payments\BasePaymentForm;
use craft\commerce\base\RequestResponseInterface;
use craft\commerce\elements\Order;
use craft\helpers\UrlHelper;
use remita\craftremitapayment\responses\PaymentResponse;

class RemitaGateway extends BaseGateway
{
    public string $secretKey = '';
    public string $baseUrl = '';

    public static function displayName(): string
    {
        return 'Remita Checkout';
    }

    public function rules(): array
    {
        $rules = parent::rules();
        $rules[] = [['secretKey', 'baseUrl'], 'required'];
        $rules[] = ['baseUrl', 'url', 'defaultScheme' => 'https'];
        return $rules;
    }

    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate(
            'remita-payment/settings',
            ['gateway' => $this]
        );
    }

    public function purchase(Transaction $transaction, BasePaymentForm $form): RequestResponseInterface
    {
        $order = $transaction->getOrder();
        $billingAddress = $order->getBillingAddress();
        
        $paymentIdentifier = uniqid('ORD-');
        
        $payload = [
            'firstName' => $billingAddress ? $billingAddress->firstName : 'Customer',
            'lastName' => $billingAddress ? $billingAddress->lastName : '',
            'email' => $order->email,
            'phoneNumber' => $billingAddress ? ($billingAddress->phone ?: '08000000000') : '08000000000',
            'paymentIdentifier' => $paymentIdentifier,
            'currency' => $order->currency,
            'narration' => "Order #{$order->number}",
            'amount' => round($transaction->amount * 100),
            'returnUrl' => UrlHelper::actionUrl('commerce/payments/complete-payment', [
                'commerceTransactionId' => $transaction->id,
                'commerceTransactionHash' => $transaction->hash
            ])
        ];

        $client = Craft::createGuzzleClient();
        $endpoint = rtrim($this->baseUrl, '/') . '/api/v1/payment/charge';

        try {
            $response = $client->request('POST', $endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'secretKey' => $this->secretKey
                ],
                'json' => $payload
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (isset($body['status']) && $body['status'] === '00' && !empty($body['data']['paymentLink'])) {
                $body['data']['paymentIdentifier'] = $paymentIdentifier;
                return new PaymentResponse($body['data'], false, true);
            }

            return new PaymentResponse(['message' => $body['message'] ?? 'Initialization failed'], false);
            
        } catch (\Exception $e) {
            Craft::error("Remita Initialize Exception: " . $e->getMessage(), __METHOD__);
            return new PaymentResponse(['message' => $e->getMessage()], false);
        }
    }

    public function completePurchase(Transaction $transaction): RequestResponseInterface
    {
        $paymentIdentifier = $transaction->reference;

        if (empty($paymentIdentifier)) {
            return new PaymentResponse(['message' => 'Missing transaction reference.'], false);
        }

        $client = Craft::createGuzzleClient();
        $endpoint = rtrim($this->baseUrl, '/') . '/api/v1/payment/query/' . $paymentIdentifier;

        try {
            $response = $client->request('GET', $endpoint, [
                'headers' => [
                    'Accept' => 'application/json',
                    'secretKey' => $this->secretKey
                ]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (isset($body['status']) && $body['status'] === '00') {
                return new PaymentResponse($body['data'], true);
            }

            return new PaymentResponse(['message' => $body['message'] ?? 'Verification failed'], false);
            
        } catch (\Exception $e) {
            Craft::error("Remita Verify Exception: " . $e->getMessage(), __METHOD__);
            return new PaymentResponse(['message' => $e->getMessage()], false);
        }
    }

    public function supportsAuthorize(): bool { return false; }
    public function supportsCapture(): bool { return false; }
    public function supportsPurchase(): bool { return true; }
    public function supportsCompletePurchase(): bool { return true; }
    public function supportsRefunds(): bool { return false; }
    public function supportsWebhooks(): bool { return false; }
    public function supportsPaymentSources(): bool { return false; }
    public function availableForUseWithOrder(Order $order): bool { return true; }
}
