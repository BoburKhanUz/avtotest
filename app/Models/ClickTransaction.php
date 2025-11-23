<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClickTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'click_trans_id',
        'merchant_trans_id',
        'amount',
        'action',
        'sign_time',
        'status',
        'error',
    ];
}
