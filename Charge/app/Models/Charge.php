<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    use HasFactory;


    protected $fillable = [
        'charge_category_id',
        'user_id',
        'company_id',
        'code',
        'expire_date',
        'sold_status',
        'status',
        'amount'
    ];
}
