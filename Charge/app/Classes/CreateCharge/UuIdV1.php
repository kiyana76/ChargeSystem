<?php

namespace App\Classes\CreateCharge;

use Laravel\Lumen\Testing\DatabaseTransactions;
use Symfony\Component\Uid\Uuid;

class UuIdV1 implements CreateChargeFactoryInterface
{
    use DatabaseTransactions;

    public function getChargeCode ()
    {
        return Uuid::v1();
    }
}
