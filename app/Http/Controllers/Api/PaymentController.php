<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Promocode;
use App\Models\Subscription;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="Avto Test Imtihon API",
 *     version="1.0.0",
 *     description="Avto Test Imtihon loyihasi uchun API hujjatlari"
 * )
 * 
 * @OA\Schema(
 *     schema="Payment",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="subscription_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="promocode_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="amount", type="number", format="float", example=20.00),
 *     @OA\Property(property="status", type="string", example="completed", enum={"pending", "completed", "failed"}),
 *     @OA\Property(property="stripe_id", type="string", example="ch_test", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-20T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-20T10:00:00Z")
 * )
 * 
 * @OA\Schema(
 *     schema="Subscription",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="plan_id", type="integer", example=1),
 *     @OA\Property(property="starts_at", type="string", format="date-time", example="2025-02-20T10:00:00Z"),
 *     @OA\Property(property="ends_at", type="string", format="date-time", example="2025-03-20T10:00:00Z"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-20T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-20T10:00:00Z")
 * )
 */
class PaymentController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/payments",
     *     summary="Yangi to‘lov yaratish",
     *     tags={"Payments"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="payment_method", type="string", example="pm_card_visa"),
     *             @OA\Property(property="plan_id", type="integer", example=1),
     *             @OA\Property(property="promocode", type="string", example="SAVE50", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="To‘lov muvaffaqiyatli yaratildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="payment", ref="#/components/schemas/Payment"),
     *             @OA\Property(property="subscription", ref="#/components/schemas/Subscription")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Xato yuz berdi",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Promokod yaroqsiz")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'payment_method' => 'required|string',
            'plan_id' => 'required|exists:plans,id',
            'promocode' => 'nullable|string|exists:promocodes,code',
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);
        $promocode = null;
        $amount = $plan->price;

        if (isset($validated['promocode'])) {
            $promocode = Promocode::where('code', $validated['promocode'])->first();
            if (!$promocode || !$promocode->isValid()) {
                return response()->json(['error' => 'Promokod yaroqsiz'], 400);
            }
            $discount = $plan->price * ($promocode->discount_percentage / 100);
            $amount = max(0, $plan->price - $discount);
        }

        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'promocode_id' => $promocode ? $promocode->id : null,
            'status' => 'pending',
        ]);

        try {
            $charge = $user->charge($amount * 100, $validated['payment_method']);
            $payment->update(['status' => 'completed', 'stripe_id' => $charge->id]);

            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'starts_at' => now(),
                'ends_at' => now()->addDays($plan->duration_days),
                'is_active' => true,
            ]);

            $payment->update(['subscription_id' => $subscription->id]);

            if ($promocode) {
                $promocode->increment('used_count');
            }

            return response()->json(['payment' => $payment, 'subscription' => $subscription], 201);
        } catch (\Exception $e) {
            $payment->update(['status' => 'failed']);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/payments",
     *     summary="Foydalanuvchi to‘lovlarini ro‘yxatini olish",
     *     tags={"Payments"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="To‘lovlar ro‘yxati",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Payment")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $payments = $request->user()->payments()->with('subscription.plan', 'promocode')->get();
        return response()->json($payments);
    }
}