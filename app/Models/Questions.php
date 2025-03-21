<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['test_id', 'content_uz', 'content_ru', 'options_uz', 'options_ru', 'correct_option'];

    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id');
    }

    public function getContentAttribute()
    {
        $lang = request()->header('Accept-Language', 'uz');
        return $this->{"content_$lang"} ?? 'No Content';
    }

    public function getOptionsAttribute()
    {
        $lang = request()->header('Accept-Language', 'uz');
        return $this->{"options_$lang"} ?? 'No Options';
    }
}