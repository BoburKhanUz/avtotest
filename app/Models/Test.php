<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = [
        'title',
        'description',
        'question_count',
        'time_limit',
        'category_id',
        'name_default',
        'name_uz',
        'name_ru',
        'name_en',
        'description_uz',
        'description_ru',
        'description_en',
    ];

    public function getNameAttribute()
    {
        $lang = request()->header('Accept-Language', 'uz');

        // Multi-language support
        if (isset($this->{"name_$lang"}) && $this->{"name_$lang"}) {
            return $this->{"name_$lang"};
        }

        // Fallback to default or title
        return $this->name_default ?? $this->title ?? 'No Name';
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'test_id', 'id');
    }

    public function testResults()
    {
        return $this->hasMany(TestResult::class);
    }

    public function testSessions()
    {
        return $this->hasMany(TestSession::class);
    }
}