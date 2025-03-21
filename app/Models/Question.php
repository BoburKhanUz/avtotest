<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id', 'content_uz', 'content_ru', 
        'options_uz', 'options_ru', 'correct_option'
    ];

    protected $casts = [
        'options_uz' => 'array',
        'options_ru' => 'array',
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}