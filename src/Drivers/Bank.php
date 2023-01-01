<?php

namespace Romero\LaravelPayments\Drivers;

interface Bank
{
    public function request($api, $amount, $callbackURL, $info_user);
    public function verify($params);
}
