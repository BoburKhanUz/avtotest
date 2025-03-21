<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Test;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index(Request $request)
    {
    $language = $request->header('Accept-Language', 'uz');
    $tests = Test::with([
        'questions' => function ($query) use ($language) {
            $query->select('id', 'test_id', "content_$language as content", "options_$language as options", 'correct_option');
        },
        'category' => function ($query) use ($language) {
            $query->select('id', "name_$language as name");
        }
    ])->select('id', "name_$language as name", 'category_id')
      ->get();

    return response()->json($tests);
    }

    public function show(Request $request, Test $test)
    {
        $language = $request->header('Accept-Language', 'uz'); // Standart til: o‘zbekcha
        $test->load([
            'questions' => function ($query) use ($language) {
                $query->select('id', 'test_id', "content_$language as content", "options_$language as options", 'correct_option');
            },
            'category' => function ($query) use ($language) {
                $query->select('id', "name_$language as name"); // Kategoriya nomini tilga qarab qaytarish
            }
        ]);

        // Test nomini tilga qarab qaytarish uchun
        $test->name = $test->{"name_$language"} ?? $test->name_default;

        return response()->json($test);
    }
}