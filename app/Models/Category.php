<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name_default', 'name_uz', 'name_ru'];

    public function getNameAttribute()
    {
        $lang = request()->header('Accept-Language', 'uz'); // Foydalanuvchi tilini olish
        return $this->{"name_$lang"} ?? $this->name_default; // Tanlangan tilda nom yoki standart nom
    }
}