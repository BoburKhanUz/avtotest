<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymeTransaction;
use Illuminate\Support\Facades\Log;

class PaymeService
{
    private $merchant_id;
    private $secret_key;
    private $endpoint;

    public function __construct()
    {
        $this->merchant_id = config('services.payme.merchant_id');
        $this->secret_key = config('services.payme.secret_key');
        $this->endpoint = config('services.payme.endpoint', 'https://checkout.paycom.uz/api');
    }

    /**
     * Payme merchant API request validatsiyasi
     */
    public function authorize($headers)
    {
        if (!isset($headers['Authorization'])) {
            throw new \Exception('Authorization header missing');
        }

        $auth = str_replace('Basic ', '', $headers['Authorization']);
        $decoded = base64_decode($auth);

        list($login, $password) = explode(':', $decoded);

        if ($login !== 'Paycom' || $password !== $this->secret_key) {
            throw new \Exception('Unauthorized');
        }

        return true;
    }

    /**
     * CheckPerformTransaction - To'lovni amalga oshirish mumkinligini tekshirish
     */
    public function checkPerformTransaction($params)
    {
        $amount = $params['amount'];
        $account = $params['account'];

        // Subscription yoki plan mavjudligini tekshirish
        if (!isset($account['plan_id'])) {
            return [
                'error' => [
                    'code' => -31050,
                    'message' => [
                        'uz' => 'Plan topilmadi',
                        'ru' => 'План не найден',
                        'en' => 'Plan not found'
                    ]
                ]
            ];
        }

        return ['allow' => true];
    }

    /**
     * CreateTransaction - Tranzaksiya yaratish
     */
    public function createTransaction($params)
    {
        $id = $params['id'];
        $time = $params['time'];
        $amount = $params['amount'];
        $account = $params['account'];

        // Mavjud tranzaksiyani tekshirish
        $transaction = PaymeTransaction::where('payme_transaction_id', $id)->first();

        if ($transaction) {
            if ($transaction->state == 1) {
                return [
                    'create_time' => $transaction->payme_time,
                    'transaction' => (string)$transaction->id,
                    'state' => 1
                ];
            }

            if ($transaction->state == -1) {
                return [
                    'error' => [
                        'code' => -31008,
                        'message' => 'Transaction cancelled'
                    ]
                ];
            }
        }

        // Yangi tranzaksiya yaratish
        $newTransaction = PaymeTransaction::create([
            'payme_transaction_id' => $id,
            'payme_time' => $time,
            'amount' => $amount,
            'account' => json_encode($account),
            'state' => 1,
            'create_time' => now(),
        ]);

        return [
            'create_time' => $time,
            'transaction' => (string)$newTransaction->id,
            'state' => 1
        ];
    }

    /**
     * PerformTransaction - To'lovni amalga oshirish
     */
    public function performTransaction($params)
    {
        $id = $params['id'];

        $transaction = PaymeTransaction::where('payme_transaction_id', $id)->first();

        if (!$transaction) {
            return [
                'error' => [
                    'code' => -31003,
                    'message' => 'Transaction not found'
                ]
            ];
        }

        if ($transaction->state == 1) {
            $transaction->update([
                'state' => 2,
                'perform_time' => now(),
            ]);

            // To'lov muvaffaqiyatli bo'ldi, subscription yaratish
            $account = json_decode($transaction->account, true);
            $this->createSubscription($transaction, $account);

            return [
                'transaction' => (string)$transaction->id,
                'perform_time' => time() * 1000,
                'state' => 2
            ];
        }

        if ($transaction->state == 2) {
            return [
                'transaction' => (string)$transaction->id,
                'perform_time' => strtotime($transaction->perform_time) * 1000,
                'state' => 2
            ];
        }

        return [
            'error' => [
                'code' => -31008,
                'message' => 'Transaction cancelled'
            ]
        ];
    }

    /**
     * CancelTransaction - Tranzaksiyani bekor qilish
     */
    public function cancelTransaction($params)
    {
        $id = $params['id'];
        $reason = $params['reason'];

        $transaction = PaymeTransaction::where('payme_transaction_id', $id)->first();

        if (!$transaction) {
            return [
                'error' => [
                    'code' => -31003,
                    'message' => 'Transaction not found'
                ]
            ];
        }

        if ($transaction->state == 1) {
            $transaction->update([
                'state' => -1,
                'cancel_time' => now(),
                'reason' => $reason,
            ]);

            return [
                'transaction' => (string)$transaction->id,
                'cancel_time' => time() * 1000,
                'state' => -1
            ];
        }

        if ($transaction->state == 2) {
            $transaction->update([
                'state' => -2,
                'cancel_time' => now(),
                'reason' => $reason,
            ]);

            // Subscription ni bekor qilish
            $this->cancelSubscription($transaction);

            return [
                'transaction' => (string)$transaction->id,
                'cancel_time' => time() * 1000,
                'state' => -2
            ];
        }

        return [
            'transaction' => (string)$transaction->id,
            'cancel_time' => strtotime($transaction->cancel_time) * 1000,
            'state' => $transaction->state
        ];
    }

    /**
     * CheckTransaction - Tranzaksiya holatini tekshirish
     */
    public function checkTransaction($params)
    {
        $id = $params['id'];

        $transaction = PaymeTransaction::where('payme_transaction_id', $id)->first();

        if (!$transaction) {
            return [
                'error' => [
                    'code' => -31003,
                    'message' => 'Transaction not found'
                ]
            ];
        }

        return [
            'create_time' => $transaction->payme_time,
            'perform_time' => $transaction->perform_time ? strtotime($transaction->perform_time) * 1000 : 0,
            'cancel_time' => $transaction->cancel_time ? strtotime($transaction->cancel_time) * 1000 : 0,
            'transaction' => (string)$transaction->id,
            'state' => $transaction->state,
            'reason' => $transaction->reason
        ];
    }

    /**
     * GetStatement - Tranzaksiyalar hisobotini olish
     */
    public function getStatement($params)
    {
        $from = $params['from'];
        $to = $params['to'];

        $transactions = PaymeTransaction::whereBetween('payme_time', [$from, $to])->get();

        $result = [];
        foreach ($transactions as $transaction) {
            $result[] = [
                'id' => $transaction->payme_transaction_id,
                'time' => $transaction->payme_time,
                'amount' => $transaction->amount,
                'account' => json_decode($transaction->account),
                'create_time' => strtotime($transaction->create_time) * 1000,
                'perform_time' => $transaction->perform_time ? strtotime($transaction->perform_time) * 1000 : 0,
                'cancel_time' => $transaction->cancel_time ? strtotime($transaction->cancel_time) * 1000 : 0,
                'transaction' => (string)$transaction->id,
                'state' => $transaction->state,
                'reason' => $transaction->reason
            ];
        }

        return ['transactions' => $result];
    }

    /**
     * Subscription yaratish
     */
    private function createSubscription($transaction, $account)
    {
        $plan = \App\Models\Plan::find($account['plan_id']);
        $user = \App\Models\User::find($account['user_id']);

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
                'amount' => $transaction->amount / 100,
                'status' => 'completed',
                'payment_method' => 'payme',
                'transaction_id' => $transaction->payme_transaction_id,
            ]);
        }
    }

    /**
     * Subscription ni bekor qilish
     */
    private function cancelSubscription($transaction)
    {
        $payment = Payment::where('transaction_id', $transaction->payme_transaction_id)->first();

        if ($payment && $payment->subscription) {
            $payment->subscription->update(['is_active' => false]);
            $payment->update(['status' => 'refunded']);
        }
    }

    /**
     * To'lov havolasi yaratish
     */
    public function generatePaymentUrl($amount, $account, $return_url)
    {
        $params = base64_encode(json_encode([
            'm' => $this->merchant_id,
            'ac' => $account,
            'a' => $amount * 100, // Tiyinda
            'c' => $return_url
        ]));

        return "https://checkout.paycom.uz/{$params}";
    }
}
