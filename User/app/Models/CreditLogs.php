<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditLogs extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'admin'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function admin() {
        return $this->belongsTo(User::class, 'admin_id', 'id');
    }
}
