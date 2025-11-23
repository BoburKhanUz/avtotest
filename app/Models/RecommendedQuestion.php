<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecommendedQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_id',
        'recommendation_type',
        'priority',
        'reason',
        'completed',
    ];

    protected $casts = [
        'completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
