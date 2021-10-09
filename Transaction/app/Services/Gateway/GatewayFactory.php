<?php

namespace App\Services\Gateway;

use App\Services\Gateway\Adapter\Zarinpal;

class GatewayFactory
{
    private $gateway;
    public function __construct($gateway = null)
    {
        $this->gateway = $gateway;
    }

    public static function select() {
        $gateway = $gateway ?? env("GATEWAY", null);
        if($gateway == 'zarinpal'){
            return new Zarinpal();
        }

    }
}
