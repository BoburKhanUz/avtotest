<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Test;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        $tests = Test::with('category')->get();
        return view('admin.tests.index', compact('tests'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.tests.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'question_count' => 'required|integer|min:1|max:20',
            'category_id' => 'nullable|exists:categories,id',
            'time_limit' => 'required|integer|min:1',
        ]);

        Test::create($validated);
        return redirect()->route('admin.tests.index')->with('success', 'Test muvaffaqiyatli qo‘shildi');
    }

    public function show(Test $test)
    {
        $test->load('category', 'questions');
        return view('admin.tests.show', compact('test'));
    }

    public function edit(Test $test)
    {
        $categories = Category::all();
        return view('admin.tests.edit', compact('test', 'categories'));
    }

    public function update(Request $request, Test $test)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'question_count' => 'required|integer|min:1|max:20',
            'category_id' => 'nullable|exists:categories,id',
            'time_limit' => 'required|integer|min:1',
        ]);

        $test->update($validated);
        return redirect()->route('admin.tests.index')->with('success', 'Test muvaffaqiyatli yangilandi');
    }

    public function destroy(Test $test)
    {
        $test->delete();
        return redirect()->route('admin.tests.index')->with('success', 'Test o‘chirildi');
    }
}