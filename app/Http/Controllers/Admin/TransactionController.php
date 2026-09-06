<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['package', 'customer'])
            ->leftJoin('wifi_users', 'transactions.wifi_user_id', '=', 'wifi_users.id')
            ->where(function($q) {
                // Online transactions
                $q->where('transactions.payment_method', '!=', 'cash')
                // Direct cash sales (e.g. manual vouchers created for customers)
                ->orWhere('transactions.phone_number', '!=', 'Batch Print')
                // Or batch printed paper vouchers that have been activated/used
                ->orWhereNotNull('wifi_users.activated_at');
            })
            ->select('transactions.*');

        // Highlight specific transaction from notification
        $highlightId = $request->input('highlight');
        if ($highlightId) {
            $query->where('transactions.id', $highlightId);
        } elseif ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('transactions.phone_number', 'like', "%{$search}%")
                  ->orWhere('transactions.transaction_reference', 'like', "%{$search}%")
                  ->orWhere('transactions.palm_pesa_order_id', 'like', "%{$search}%")
                  ->orWhere('transactions.mikrotik_voucher', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filters
        if ($status = $request->input('status')) {
            $query->where('transactions.status', $status);
        }
        if ($method = $request->input('method')) {
            $query->where('transactions.payment_method', $method);
        }

        $transactions = (clone $query)
            ->orderBy(\Illuminate\Support\Facades\DB::raw('COALESCE(wifi_users.activated_at, transactions.created_at)'), 'desc')
            ->paginate(20)
            ->withQueryString();

        // Calculate Revenue (Online completed + Direct manual cash sales + Activated batch vouchers)
        $onlineRevenue = Transaction::where('status', 'success')
            ->where('payment_method', '!=', 'cash')
            ->sum('amount');

        $offlineRevenue = Transaction::where('transactions.status', 'success')
            ->where('transactions.payment_method', 'cash')
            ->leftJoin('wifi_users', 'transactions.wifi_user_id', '=', 'wifi_users.id')
            ->where(function($q) {
                $q->where('transactions.phone_number', '!=', 'Batch Print')
                  ->orWhereNotNull('wifi_users.activated_at');
            })
            ->sum('transactions.amount');

        $todayOnline = Transaction::where('status', 'success')
            ->where('payment_method', '!=', 'cash')
            ->whereDate('created_at', today())
            ->sum('amount');

        $todayOffline = Transaction::where('transactions.status', 'success')
            ->where('transactions.payment_method', 'cash')
            ->leftJoin('wifi_users', 'transactions.wifi_user_id', '=', 'wifi_users.id')
            ->where(function($q) {
                $q->where(function($directQ) {
                    $directQ->where('transactions.phone_number', '!=', 'Batch Print')
                            ->whereDate('transactions.created_at', today());
                })
                ->orWhere(function($batchQ) {
                    $batchQ->where('transactions.phone_number', 'Batch Print')
                           ->whereNotNull('wifi_users.activated_at')
                           ->whereDate('wifi_users.activated_at', today());
                });
            })
            ->sum('transactions.amount');

        $stats = [
            'total'     => (clone $query)->count(),
            'success'   => (clone $query)->where('transactions.status', 'success')->count(),
            'revenue'   => $onlineRevenue + $offlineRevenue,
            'today_rev' => $todayOnline + $todayOffline,
        ];

        return view('admin.transactions', compact('transactions', 'stats', 'search', 'status', 'method', 'highlightId'));
    }
}
