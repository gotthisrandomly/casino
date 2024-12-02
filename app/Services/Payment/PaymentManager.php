<?php

namespace App\Services\Payment;

use Illuminate\Support\Manager;
use App\Services\Payment\Providers\StripeProvider;
use App\Services\Payment\Providers\PayPalProvider;
use App\Services\Payment\Providers\CryptoProvider;

class PaymentManager extends Manager
{
    public function getDefaultDriver()
    {
        return config('payment.default');
    }

    public function createStripeDriver()
    {
        return new StripeProvider(
            config('payment.providers.stripe')
        );
    }

    public function createPayPalDriver()
    {
        return new PayPalProvider(
            config('payment.providers.paypal')
        );
    }

    public function createCryptoDriver()
    {
        return new CryptoProvider(
            config('payment.providers.crypto')
        );
    }
}