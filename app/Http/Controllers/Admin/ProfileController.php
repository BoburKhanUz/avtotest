<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users')); // Foydalanuvchilar ro‘yxatini ko‘rsatish
    }

    public function show(User $user)
    {
        return view('admin.profile.show', compact('user')); // Foydalanuvchi profilini ko‘rsatish
    }

    public function create()
    {
        return view('admin.profile.create'); // Yangi foydalanuvchi yaratish sahifasi
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'is_admin' => 'boolean',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'is_admin' => $validated['is_admin'] ?? false,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Yangi foydalanuvchi muvaffaqiyatli qo‘shildi.');
    }

    public function edit(User $user)
    {
        return view('admin.profile.edit', compact('user')); // Foydalanuvchi ma‘lumotlarini tahrirlash
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        return redirect()->route('admin.profile.show', $user)->with('success', 'Foydalanuvchi ma‘lumotlari muvaffaqiyatli yangilandi.');
    }

    public function generatePassword(User $user)
    {
        $newPassword = substr(uniqid(), 0, 8); // Tasodifiy 8 harfli parol
        $user->update(['password' => Hash::make($newPassword)]);
        
        return redirect()->route('admin.profile.show', $user)->with('success', 'Yangi parol generatsiya qilindi: ' . $newPassword);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Foydalanuvchi muvaffaqiyatli o‘chirildi.');
    }
}