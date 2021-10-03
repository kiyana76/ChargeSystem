<?php

namespace App\Rules;

class IranianMobileNumber
{
    public function passes ($attribute, $value)
    {
        // Check if it's Iran phone number format or not
        if (substr($value, 0, 2) == "00" || substr($value, 0, 1) == "+") {
            return preg_match('/^(\+|00)[0-9]{1,3}[0-9]{4,14}(?:x.+)?$/m', $value);
        } else {
            return preg_match('/^(?:0|\+?98)9[0-9]{9}$/m', $value);
        }
    }

}
