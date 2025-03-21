<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:bepul,sinov,premium',
        ]);

        $user = $request->user();
        $startsAt = now();
        $endsAt = match ($validated['type']) {
            'bepul' => null,
            'sinov' => now()->addDays(3),
            'premium' => now()->addMonth(),
        };

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'type' => $validated['type'],
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);

        return response()->json($subscription, 201);
    }

    public function index(Request $request)
    {
        $subscriptions = $request->user()->subscriptions;
        return response()->json($subscriptions);
    }
}