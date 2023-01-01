<?php

namespace Romero\LaravelPayments\Providers;

use Romero\LaravelPayments\IRPayment;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register ()
    {
      $this->app->bind('payments', function () {
            return new IRPayment();
        });
    }

    public function boot ()
    {
        $this->publishes([
            realpath(__DIR__ . '/../config/payments.php') =>  config_path('payments.php')
        ], 'config');
    }
}
