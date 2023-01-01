<?php
namespace Romero\LaravelPayments\Facades;
use Illuminate\Support\Facades\Facade;

class IRPayment extends Facade
{
    /**
     * @return string
     */

    protected static function getFacadeAccessor()
    {
        return 'irpayment';
    }
}
