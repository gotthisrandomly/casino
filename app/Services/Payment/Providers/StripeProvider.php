<?php

namespace App\Services\Payment\Providers;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Refund;
use Exception;

class StripeProvider implements PaymentProviderInterface
{
    protected $config;
    protected $stripe;

    public function __construct(array $config)
    {
        $this->config = $config;
        Stripe::setApiKey($config['secret_key']);
    }

    public function processDeposit(float $amount, array $paymentData): array
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount * 100, // Convert to cents
                'currency' => $this->config['currency'],
                'payment_method_types' => ['card'],
                'payment_method' => $paymentData['payment_method_id'] ?? null,
                'confirm' => true,
                'return_url' => $this->config['return_url'],
            ]);

            return [
                'success' => true,
                'payment_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
                'amount' => $amount,
                'currency' => $this->config['currency'],
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function processWithdrawal(float $amount, array $withdrawalData): array
    {
        try {
            // Implement payout to connected account or bank account
            $payout = \Stripe\Payout::create([
                'amount' => $amount * 100,
                'currency' => $this->config['currency'],
                'method' => $withdrawalData['method'],
                'destination' => $withdrawalData['destination'],
            ]);

            return [
                'success' => true,
                'payout_id' => $payout->id,
                'status' => $payout->status,
                'amount' => $amount,
                'currency' => $this->config['currency'],
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function verifyPayment(string $paymentId): bool
    {
        try {
            $payment = PaymentIntent::retrieve($paymentId);
            return $payment->status === 'succeeded';
        } catch (Exception $e) {
            return false;
        }
    }

    public function getPaymentStatus(string $paymentId): string
    {
        try {
            $payment = PaymentIntent::retrieve($paymentId);
            return $payment->status;
        } catch (Exception $e) {
            return 'error';
        }
    }

    public function refundPayment(string $paymentId, ?float $amount = null): array
    {
        try {
            $refundData = ['payment_intent' => $paymentId];
            
            if ($amount !== null) {
                $refundData['amount'] = $amount * 100;
            }

            $refund = Refund::create($refundData);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'status' => $refund->status,
                'amount' => $amount ?? ($refund->amount / 100),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getAvailablePaymentMethods(): array
    {
        return [
            'card' => [
                'name' => 'Credit/Debit Card',
                'currencies' => ['USD', 'EUR', 'GBP'],
                'min_amount' => 10,
                'max_amount' => 10000,
            ],
        ];
    }
}