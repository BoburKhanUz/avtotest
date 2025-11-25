<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Test;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index(Request $request)
    {
        $query = Test::with('category')->withCount('questions');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('name_uz', 'like', "%{$search}%")
                  ->orWhere('name_ru', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $tests = $query->paginate(20)->withQueryString();
        $categories = Category::all();

        return view('admin.tests.index', compact('tests', 'categories'));
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
            'name_uz' => 'nullable|string|max:255',
            'name_ru' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_uz' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'description_en' => 'nullable|string',
            'question_count' => 'required|integer|min:1|max:50',
            'category_id' => 'required|exists:categories,id',
            'time_limit' => 'required|integer|min:1|max:180',
        ]);

        // Set name_default from title if not provided
        if (!isset($validated['name_default']) && isset($validated['title'])) {
            $validated['name_default'] = $validated['title'];
        }

        Test::create($validated);
        return redirect()->route('admin.tests.index')->with('success', 'Test muvaffaqiyatli qo'shildi');
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
            'name_uz' => 'nullable|string|max:255',
            'name_ru' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_uz' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'description_en' => 'nullable|string',
            'question_count' => 'required|integer|min:1|max:50',
            'category_id' => 'required|exists:categories,id',
            'time_limit' => 'required|integer|min:1|max:180',
        ]);

        // Update name_default from title if not provided
        if (!isset($validated['name_default']) && isset($validated['title'])) {
            $validated['name_default'] = $validated['title'];
        }

        $test->update($validated);
        return redirect()->route('admin.tests.index')->with('success', 'Test muvaffaqiyatli yangilandi');
    }

    public function destroy(Test $test)
    {
        $test->delete();
        return redirect()->route('admin.tests.index')->with('success', 'Test o‘chirildi');
    }
}