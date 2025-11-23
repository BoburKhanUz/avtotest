<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Test;
use Illuminate\Http\Request;

class TestController extends Controller
{
    protected function validateLanguage($language)
    {
        $allowedLanguages = ['uz', 'ru', 'en'];
        return in_array($language, $allowedLanguages) ? $language : 'uz';
    }

    public function index(Request $request)
    {
        $language = $this->validateLanguage($request->header('Accept-Language', 'uz'));

        $tests = Test::with([
            'questions' => function ($query) use ($language) {
                $query->select('id', 'test_id', "content_{$language} as content", "options_{$language} as options", 'correct_option');
            },
            'category' => function ($query) use ($language) {
                $query->select('id', "name_{$language} as name");
            }
        ])->select('id', "name_{$language} as name", 'category_id')
          ->get();

        return response()->json($tests);
    }

    public function show(Request $request, Test $test)
    {
        $language = $this->validateLanguage($request->header('Accept-Language', 'uz'));

        $test->load([
            'questions' => function ($query) use ($language) {
                $query->select('id', 'test_id', "content_{$language} as content", "options_{$language} as options", 'correct_option');
            },
            'category' => function ($query) use ($language) {
                $query->select('id', "name_{$language} as name");
            }
        ]);

        $test->name = $test->{"name_{$language}"} ?? $test->name_uz;

        return response()->json($test);
    }
}