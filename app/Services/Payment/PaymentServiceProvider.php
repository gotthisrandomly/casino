<?php

namespace App\Services\Payment;

use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PaymentManager::class, function ($app) {
            return new PaymentManager($app);
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/payment.php' => config_path('payment.php'),
        ], 'payment-config');
    }
}