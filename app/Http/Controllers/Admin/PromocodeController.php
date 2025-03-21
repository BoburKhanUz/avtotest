<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promocode;
use Illuminate\Http\Request;

class PromocodeController extends Controller
{
    public function index()
    {
        $promocodes = Promocode::all();
        return view('admin.promocodes.index', compact('promocodes'));
    }

    public function create()
    {
        return view('admin.promocodes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:promocodes,code|max:20',
            'discount_percentage' => 'required|integer|min:1|max:100',
            'expires_at' => 'nullable|date|after:now',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        Promocode::create($validated);
        return redirect()->route('admin.promocodes.index')->with('success', 'Promokod muvaffaqiyatli qo‘shildi');
    }

    public function edit(Promocode $promocode)
    {
        return view('admin.promocodes.edit', compact('promocode'));
    }

    public function update(Request $request, Promocode $promocode)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:promocodes,code,' . $promocode->id,
            'discount_percentage' => 'required|integer|min:1|max:100',
            'expires_at' => 'nullable|date|after:now',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $promocode->update($validated);
        return redirect()->route('admin.promocodes.index')->with('success', 'Promokod muvaffaqiyatli yangilandi');
    }

    public function destroy(Promocode $promocode)
    {
        $promocode->delete();
        return redirect()->route('admin.promocodes.index')->with('success', 'Promokod o‘chirildi');
    }
}