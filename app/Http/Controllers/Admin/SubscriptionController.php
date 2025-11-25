<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with(['user', 'plan']);

        // Search by user
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by plan
        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        // Filter by expiry
        if ($request->filled('expiry')) {
            if ($request->expiry === 'expired') {
                $query->where('expires_at', '<', now());
            } elseif ($request->expiry === 'expiring_soon') {
                $query->whereBetween('expires_at', [now(), now()->addDays(7)]);
            } elseif ($request->expiry === 'valid') {
                $query->where('expires_at', '>', now()->addDays(7));
            }
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $subscriptions = $query->paginate(20)->withQueryString();
        $plans = Plan::all();

        $stats = [
            'total' => Subscription::count(),
            'active' => Subscription::where('is_active', true)->count(),
            'expired' => Subscription::where('is_active', false)->where('expires_at', '<', now())->count(),
            'expiring_soon' => Subscription::where('is_active', true)->whereBetween('expires_at', [now(), now()->addDays(7)])->count(),
        ];

        return view('admin.subscriptions.index', compact('subscriptions', 'plans', 'stats'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        $plans = Plan::where('is_active', true)->get();
        return view('admin.subscriptions.create', compact('users', 'plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'duration_days' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);

        $subscription = Subscription::create([
            'user_id' => $validated['user_id'],
            'plan_id' => $validated['plan_id'],
            'started_at' => now(),
            'expires_at' => now()->addDays($validated['duration_days']),
            'is_active' => $request->boolean('is_active', true),
            'amount_paid' => $plan->price,
        ]);

        return redirect()->route('admin.subscriptions.index')->with('success', 'Obuna muvaffaqiyatli yaratildi');
    }

    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'plan']);
        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function edit(Subscription $subscription)
    {
        $users = User::orderBy('name')->get();
        $plans = Plan::where('is_active', true)->get();
        return view('admin.subscriptions.edit', compact('subscription', 'users', 'plans'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'expires_at' => 'required|date|after:started_at',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $subscription->update($validated);
        return redirect()->route('admin.subscriptions.index')->with('success', 'Obuna muvaffaqiyatli yangilandi');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return redirect()->route('admin.subscriptions.index')->with('success', 'Obuna muvaffaqiyatli o\'chirildi');
    }

    /**
     * Deactivate subscription
     */
    public function deactivate(Subscription $subscription)
    {
        $subscription->update(['is_active' => false]);
        return redirect()->back()->with('success', 'Obuna o\'chirildi');
    }

    /**
     * Activate subscription
     */
    public function activate(Subscription $subscription)
    {
        $subscription->update(['is_active' => true]);
        return redirect()->back()->with('success', 'Obuna faollashtirildi');
    }

    /**
     * Extend subscription
     */
    public function extend(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $subscription->expires_at = Carbon::parse($subscription->expires_at)->addDays($validated['days']);
        $subscription->save();

        return redirect()->back()->with('success', "Obuna {$validated['days']} kunga uzaytirildi");
    }
}
