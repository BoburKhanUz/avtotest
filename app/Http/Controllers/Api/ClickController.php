<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ClickService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClickController extends Controller
{
    private $clickService;

    public function __construct(ClickService $clickService)
    {
        $this->clickService = $clickService;
    }

    /**
     * Click prepare endpoint
     */
    public function prepare(Request $request)
    {
        Log::info('Click prepare request', $request->all());

        $response = $this->clickService->prepare($request->all());

        Log::info('Click prepare response', $response);

        return response()->json($response);
    }

    /**
     * Click complete endpoint
     */
    public function complete(Request $request)
    {
        Log::info('Click complete request', $request->all());

        $response = $this->clickService->complete($request->all());

        Log::info('Click complete response', $response);

        return response()->json($response);
    }

    /**
     * To'lov havolasini yaratish (foydalanuvchi uchun)
     */
    public function createPayment(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'return_url' => 'nullable|url'
        ]);

        $user = $request->user();
        $plan = \App\Models\Plan::findOrFail($validated['plan_id']);

        $return_url = $validated['return_url'] ?? url('/payment/success');

        $payment_url = $this->clickService->generatePaymentUrl(
            $user->id,
            $plan->id,
            $plan->price,
            $return_url
        );

        return response()->json([
            'payment_url' => $payment_url,
            'amount' => $plan->price,
            'plan' => $plan->name_uz
        ]);
    }
}
