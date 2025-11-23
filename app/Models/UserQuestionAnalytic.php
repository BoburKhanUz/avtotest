<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuestionAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_id',
        'correct_count',
        'incorrect_count',
        'total_attempts',
        'success_rate',
        'last_attempt_at',
        'mastered',
    ];

    protected $casts = [
        'last_attempt_at' => 'datetime',
        'mastered' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Muvaffaqiyat foizini hisoblash
     */
    public function calculateSuccessRate()
    {
        if ($this->total_attempts == 0) {
            return 0;
        }

        return ($this->correct_count / $this->total_attempts) * 100;
    }

    /**
     * Savolni o'zlashtirilgan deb belgilash (80% dan yuqori)
     */
    public function checkMastery()
    {
        $this->mastered = $this->success_rate >= 80 && $this->total_attempts >= 3;
        $this->save();
    }
}
