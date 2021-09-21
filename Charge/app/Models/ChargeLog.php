<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargeLog extends Model
{
    use HasFactory;


    protected $fillable = [
        'charge_id',
        'code',
        'mobile',
        'status'
    ];
}
