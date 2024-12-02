<?php

namespace App\Services\Payment\Providers;

interface PaymentProviderInterface
{
    /**
     * Process a deposit
     */
    public function processDeposit(float $amount, array $paymentData): array;

    /**
     * Process a withdrawal
     */
    public function processWithdrawal(float $amount, array $withdrawalData): array;

    /**
     * Verify a payment
     */
    public function verifyPayment(string $paymentId): bool;

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $paymentId): string;

    /**
     * Refund a payment
     */
    public function refundPayment(string $paymentId, ?float $amount = null): array;

    /**
     * Get available payment methods
     */
    public function getAvailablePaymentMethods(): array;
}