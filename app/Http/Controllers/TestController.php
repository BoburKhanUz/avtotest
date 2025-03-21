<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\Category;
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
            'name_uz' => 'required|string|max:255',
            'name_ru' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        Test::create([
            'name_default' => $validated['name_uz'],
            'name_uz' => $validated['name_uz'],
            'name_ru' => $validated['name_ru'],
            'category_id' => $validated['category_id'],
        ]);

        return redirect()->route('admin.tests.index')->with('success', 'Test muvaffaqiyatli qo‘shildi.');
    }

    public function edit(Test $test)
    {
        $categories = Category::all();
        return view('admin.tests.edit', compact('test', 'categories'));
    }

    public function update(Request $request, Test $test)
    {
        $validated = $request->validate([
            'name_uz' => 'required|string|max:255',
            'name_ru' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        $test->update([
            'name_default' => $validated['name_uz'],
            'name_uz' => $validated['name_uz'],
            'name_ru' => $validated['name_ru'],
            'category_id' => $validated['category_id'],
        ]);

        return redirect()->route('admin.tests.index')->with('success', 'Test muvaffaqiyatli yangilandi.');
    }

    public function destroy(Test $test)
    {
        $test->delete();
        return redirect()->route('admin.tests.index')->with('success', 'Test muvaffaqiyatli o‘chirildi.');
    }
}