<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Models\PaymeTransaction;
use App\Models\ClickTransaction;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['user']);

        // Search by user
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $payments = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total_amount' => Payment::where('status', 'completed')->sum('amount'),
            'total_count' => Payment::count(),
            'completed' => Payment::where('status', 'completed')->count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'failed' => Payment::where('status', 'failed')->count(),
            'cancelled' => Payment::where('status', 'cancelled')->count(),
            'payme_total' => Payment::where('payment_method', 'payme')->where('status', 'completed')->sum('amount'),
            'click_total' => Payment::where('payment_method', 'click')->where('status', 'completed')->sum('amount'),
        ];

        // Monthly revenue
        $monthlyRevenue = Payment::where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        return view('admin.payments.index', compact('payments', 'stats', 'monthlyRevenue'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['user']);

        // Get payment gateway transaction details
        $gatewayTransaction = null;
        if ($payment->payment_method === 'payme') {
            $gatewayTransaction = PaymeTransaction::where('payment_id', $payment->id)->first();
        } elseif ($payment->payment_method === 'click') {
            $gatewayTransaction = ClickTransaction::where('payment_id', $payment->id)->first();
        }

        return view('admin.payments.show', compact('payment', 'gatewayTransaction'));
    }

    /**
     * Refund a payment
     */
    public function refund(Request $request, Payment $payment)
    {
        if ($payment->status !== 'completed') {
            return redirect()->back()->with('error', 'Faqat to\'langan to\'lovlarni qaytarish mumkin');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // Update payment status
        $payment->update([
            'status' => 'refunded',
            'refund_reason' => $validated['reason'],
            'refunded_at' => now(),
        ]);

        // Deactivate related subscription
        if ($payment->user) {
            $payment->user->subscriptions()
                ->where('amount_paid', $payment->amount)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        return redirect()->back()->with('success', 'To\'lov muvaffaqiyatli qaytarildi');
    }

    /**
     * Cancel a pending payment
     */
    public function cancel(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return redirect()->back()->with('error', 'Faqat kutilayotgan to\'lovlarni bekor qilish mumkin');
        }

        $payment->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'To\'lov bekor qilindi');
    }

    /**
     * Export payments to CSV
     */
    public function export(Request $request)
    {
        $query = Payment::with(['user']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        $filename = 'payments_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($file, ['ID', 'Foydalanuvchi', 'Summa', 'To\'lov turi', 'Status', 'Transaction ID', 'Sana']);

            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->user ? $payment->user->name : 'N/A',
                    $payment->amount,
                    $payment->payment_method,
                    $payment->status,
                    $payment->transaction_id,
                    $payment->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
