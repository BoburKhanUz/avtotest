<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymeController extends Controller
{
    private $paymeService;

    public function __construct(PaymeService $paymeService)
    {
        $this->paymeService = $paymeService;
    }

    /**
     * Payme Merchant API endpoint
     */
    public function handle(Request $request)
    {
        try {
            // Autorizatsiyani tekshirish
            $this->paymeService->authorize($request->header());

            $data = $request->all();
            $method = $data['method'] ?? null;
            $params = $data['params'] ?? [];

            Log::info('Payme request', ['method' => $method, 'params' => $params]);

            $response = match ($method) {
                'CheckPerformTransaction' => $this->paymeService->checkPerformTransaction($params),
                'CreateTransaction' => $this->paymeService->createTransaction($params),
                'PerformTransaction' => $this->paymeService->performTransaction($params),
                'CancelTransaction' => $this->paymeService->cancelTransaction($params),
                'CheckTransaction' => $this->paymeService->checkTransaction($params),
                'GetStatement' => $this->paymeService->getStatement($params),
                default => [
                    'error' => [
                        'code' => -32601,
                        'message' => 'Method not found'
                    ]
                ]
            };

            Log::info('Payme response', $response);

            return response()->json([
                'jsonrpc' => '2.0',
                'id' => $data['id'] ?? 0,
                'result' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('Payme error: ' . $e->getMessage());

            return response()->json([
                'jsonrpc' => '2.0',
                'id' => $request->input('id', 0),
                'error' => [
                    'code' => -32504,
                    'message' => $e->getMessage()
                ]
            ]);
        }
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

        $account = [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ];

        $return_url = $validated['return_url'] ?? url('/payment/success');

        $payment_url = $this->paymeService->generatePaymentUrl(
            $plan->price,
            $account,
            $return_url
        );

        return response()->json([
            'payment_url' => $payment_url,
            'amount' => $plan->price,
            'plan' => $plan->name_uz
        ]);
    }
}
