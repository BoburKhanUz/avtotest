<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\ClickTransaction;
use Illuminate\Support\Facades\Log;

class ClickService
{
    private $merchant_id;
    private $service_id;
    private $secret_key;

    public function __construct()
    {
        $this->merchant_id = config('services.click.merchant_id');
        $this->service_id = config('services.click.service_id');
        $this->secret_key = config('services.click.secret_key');
    }

    /**
     * Prepare - To'lovni tayyorlash
     */
    public function prepare($params)
    {
        $click_trans_id = $params['click_trans_id'];
        $service_id = $params['service_id'];
        $merchant_trans_id = $params['merchant_trans_id'];
        $amount = $params['amount'];
        $action = $params['action'];
        $sign_time = $params['sign_time'];
        $sign_string = $params['sign_string'];

        // Service ID tekshirish
        if ($service_id != $this->service_id) {
            return [
                'click_trans_id' => $click_trans_id,
                'merchant_trans_id' => $merchant_trans_id,
                'error' => -5,
                'error_note' => 'Service ID is incorrect'
            ];
        }

        // Sign string tekshirish
        $expected_sign = md5(
            $click_trans_id .
            $service_id .
            $this->secret_key .
            $merchant_trans_id .
            $amount .
            $action .
            $sign_time
        );

        if ($sign_string !== $expected_sign) {
            return [
                'click_trans_id' => $click_trans_id,
                'merchant_trans_id' => $merchant_trans_id,
                'error' => -1,
                'error_note' => 'Sign check failed'
            ];
        }

        // Plan ID va User ID ni merchant_trans_id dan ajratib olish
        // Format: {user_id}_{plan_id}
        list($user_id, $plan_id) = explode('_', $merchant_trans_id);

        $plan = \App\Models\Plan::find($plan_id);
        $user = \App\Models\User::find($user_id);

        if (!$plan || !$user) {
            return [
                'click_trans_id' => $click_trans_id,
                'merchant_trans_id' => $merchant_trans_id,
                'error' => -5,
                'error_note' => 'User or Plan not found'
            ];
        }

        // Miqdorni tekshirish
        if ($amount != $plan->price) {
            return [
                'click_trans_id' => $click_trans_id,
                'merchant_trans_id' => $merchant_trans_id,
                'error' => -2,
                'error_note' => 'Incorrect amount'
            ];
        }

        // Tranzaksiyani yaratish yoki yangilash
        $transaction = ClickTransaction::updateOrCreate(
            ['click_trans_id' => $click_trans_id],
            [
                'merchant_trans_id' => $merchant_trans_id,
                'amount' => $amount,
                'action' => $action,
                'sign_time' => $sign_time,
                'status' => 0, // 0 = prepared
            ]
        );

        return [
            'click_trans_id' => $click_trans_id,
            'merchant_trans_id' => $merchant_trans_id,
            'merchant_prepare_id' => $transaction->id,
            'error' => 0,
            'error_note' => 'Success'
        ];
    }

    /**
     * Complete - To'lovni yakunlash
     */
    public function complete($params)
    {
        $click_trans_id = $params['click_trans_id'];
        $service_id = $params['service_id'];
        $merchant_trans_id = $params['merchant_trans_id'];
        $merchant_prepare_id = $params['merchant_prepare_id'];
        $amount = $params['amount'];
        $action = $params['action'];
        $sign_time = $params['sign_time'];
        $sign_string = $params['sign_string'];
        $error = $params['error'];

        // Service ID tekshirish
        if ($service_id != $this->service_id) {
            return [
                'click_trans_id' => $click_trans_id,
                'merchant_trans_id' => $merchant_trans_id,
                'error' => -5,
                'error_note' => 'Service ID is incorrect'
            ];
        }

        // Sign string tekshirish
        $expected_sign = md5(
            $click_trans_id .
            $service_id .
            $this->secret_key .
            $merchant_trans_id .
            $merchant_prepare_id .
            $amount .
            $action .
            $sign_time
        );

        if ($sign_string !== $expected_sign) {
            return [
                'click_trans_id' => $click_trans_id,
                'merchant_trans_id' => $merchant_trans_id,
                'error' => -1,
                'error_note' => 'Sign check failed'
            ];
        }

        // Prepare qilingan tranzaksiyani topish
        $transaction = ClickTransaction::where('click_trans_id', $click_trans_id)
            ->where('id', $merchant_prepare_id)
            ->first();

        if (!$transaction) {
            return [
                'click_trans_id' => $click_trans_id,
                'merchant_trans_id' => $merchant_trans_id,
                'error' => -6,
                'error_note' => 'Transaction not found'
            ];
        }

        // Agar Click tomonidan xatolik bo'lsa
        if ($error < 0) {
            $transaction->update([
                'status' => -1, // -1 = failed
                'error' => $error
            ]);

            return [
                'click_trans_id' => $click_trans_id,
                'merchant_trans_id' => $merchant_trans_id,
                'merchant_confirm_id' => $transaction->id,
                'error' => -9,
                'error_note' => 'Transaction cancelled'
            ];
        }

        // Agar tranzaksiya allaqachon yakunlangan bo'lsa
        if ($transaction->status == 1) {
            return [
                'click_trans_id' => $click_trans_id,
                'merchant_trans_id' => $merchant_trans_id,
                'merchant_confirm_id' => $transaction->id,
                'error' => 0,
                'error_note' => 'Success'
            ];
        }

        // Tranzaksiyani yakunlash
        $transaction->update([
            'status' => 1, // 1 = completed
            'action' => $action,
        ]);

        // Subscription yaratish
        list($user_id, $plan_id) = explode('_', $merchant_trans_id);
        $this->createSubscription($transaction, $user_id, $plan_id);

        return [
            'click_trans_id' => $click_trans_id,
            'merchant_trans_id' => $merchant_trans_id,
            'merchant_confirm_id' => $transaction->id,
            'error' => 0,
            'error_note' => 'Success'
        ];
    }

    /**
     * Subscription yaratish
     */
    private function createSubscription($transaction, $user_id, $plan_id)
    {
        $plan = \App\Models\Plan::find($plan_id);
        $user = \App\Models\User::find($user_id);

        if ($plan && $user) {
            $subscription = \App\Models\Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'starts_at' => now(),
                'ends_at' => now()->addDays($plan->duration_days),
                'is_active' => true,
            ]);

            Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'amount' => $transaction->amount,
                'status' => 'completed',
                'payment_method' => 'click',
                'transaction_id' => $transaction->click_trans_id,
            ]);
        }
    }

    /**
     * To'lov havolasini yaratish
     */
    public function generatePaymentUrl($user_id, $plan_id, $amount, $return_url)
    {
        $merchant_trans_id = "{$user_id}_{$plan_id}";

        $params = [
            'service_id' => $this->service_id,
            'merchant_id' => $this->merchant_id,
            'amount' => $amount,
            'transaction_param' => $merchant_trans_id,
            'return_url' => $return_url,
        ];

        return 'https://my.click.uz/services/pay?' . http_build_query($params);
    }
}
