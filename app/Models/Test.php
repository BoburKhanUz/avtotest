<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = ['name_default', 'name_uz', 'name_ru', 'category_id'];
    
    public function getNameAttribute()
    {
        $lang = request()->header('Accept-Language', 'uz');
        return $this->{"name_$lang"} ?? $this->name_default ?? 'No Name';
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'test_id', 'id');
    }
}