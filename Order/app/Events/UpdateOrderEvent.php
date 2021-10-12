<?php

namespace App\Events;

class UpdateOrderEvent extends Event
{
    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
}
