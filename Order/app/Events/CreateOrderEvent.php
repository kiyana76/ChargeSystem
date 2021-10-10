<?php

namespace App\Events;

class CreateOrderEvent extends Event
{
    public $data;
    public function __construct($order)
    {
        $this->data = $order;
    }
}
