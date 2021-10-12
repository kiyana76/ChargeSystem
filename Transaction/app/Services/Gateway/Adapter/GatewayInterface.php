<?php

namespace App\Services\Gateway\Adapter;

interface GatewayInterface
{
    public function pay($paload = []);
    public function verify($AuthId);

}
