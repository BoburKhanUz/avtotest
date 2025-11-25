<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            if ($request->role === 'admin') {
                $query->where('is_admin', true);
            } elseif ($request->role === 'user') {
                $query->where('is_admin', false);
            }
        }

        // Filter by language
        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }

        // Filter by subscription status
        if ($request->filled('subscription')) {
            if ($request->subscription === 'active') {
                $query->whereHas('subscriptions', function ($q) {
                    $q->where('is_active', true);
                });
            } elseif ($request->subscription === 'inactive') {
                $query->whereDoesntHave('subscriptions', function ($q) {
                    $q->where('is_active', true);
                });
            }
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->withCount(['subscriptions', 'testResults', 'testSessions'])
                       ->paginate(20)
                       ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::defaults()],
            'is_admin' => 'boolean',
            'language' => 'required|in:uz,ru,en',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_admin'] = $request->boolean('is_admin');

        User::create($validated);
        return redirect()->route('admin.users.index')->with('success', 'Foydalanuvchi muvaffaqiyatli yaratildi');
    }

    public function show(User $user)
    {
        $user->load([
            'subscriptions' => function ($query) {
                $query->with('plan')->latest();
            },
            'testResults' => function ($query) {
                $query->with('test')->latest()->limit(10);
            },
            'testSessions' => function ($query) {
                $query->with('test')->latest()->limit(10);
            }
        ]);

        $stats = [
            'total_tests' => $user->testResults()->count(),
            'average_score' => $user->testResults()->avg('score'),
            'best_score' => $user->testResults()->max('score'),
            'total_time' => $user->testSessions()->whereNotNull('ended_at')->sum(\DB::raw('TIMESTAMPDIFF(MINUTE, started_at, ended_at)')),
            'active_subscriptions' => $user->subscriptions()->where('is_active', true)->count(),
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'is_admin' => 'boolean',
            'language' => 'required|in:uz,ru,en',
        ]);

        // Update password only if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $validated['is_admin'] = $request->boolean('is_admin');

        $user->update($validated);
        return redirect()->route('admin.users.index')->with('success', 'Foydalanuvchi muvaffaqiyatli yangilandi');
    }

    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Siz o\'zingizni o\'chira olmaysiz');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Foydalanuvchi muvaffaqiyatli o\'chirildi');
    }
}