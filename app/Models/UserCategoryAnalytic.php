<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCategoryAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'total_questions',
        'mastered_questions',
        'category_progress',
        'average_success_rate',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
