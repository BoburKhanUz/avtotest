<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'test_id', 'started_at', 'ended_at', 'time_limit', 
        'user_answers', 'score', 'status'
    ];

    protected $casts = [
        'user_answers' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function isTimeExpired()
    {
        if (!$this->time_limit || !$this->started_at) {
            return false;
        }

        $endTime = $this->started_at->addMinutes($this->time_limit);
        return now()->greaterThan($endTime);
    }

    public function getRemainingTime()
    {
        if (!$this->time_limit || !$this->started_at) {
            return null;
        }

        $endTime = $this->started_at->addMinutes($this->time_limit);
        $remainingSeconds = now()->diffInSeconds($endTime, false);

        return max(0, $remainingSeconds);
    }
}