<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymeTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'payme_transaction_id',
        'payme_time',
        'amount',
        'account',
        'state',
        'create_time',
        'perform_time',
        'cancel_time',
        'reason',
    ];

    protected $casts = [
        'account' => 'array',
        'create_time' => 'datetime',
        'perform_time' => 'datetime',
        'cancel_time' => 'datetime',
    ];
}
