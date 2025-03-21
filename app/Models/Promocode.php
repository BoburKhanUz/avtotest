<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocode extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'discount_percentage', 'expires_at', 'is_active', 'usage_limit', 'used_count'];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function isValid()
    {
        return $this->is_active &&
               (!$this->expires_at || $this->expires_at->isFuture()) &&
               (!$this->usage_limit || $this->used_count < $this->usage_limit);
    }
}