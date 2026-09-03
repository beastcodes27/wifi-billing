<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\WifiUser;
use App\Models\Expense;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Online Revenue (Money received via Mobile Money / FimiPay / PalmPesa)
        $onlineRevenue = Transaction::where('status', 'success')
            ->where('payment_method', '!=', 'cash')
            ->sum('amount');

        // 2. Offline Revenue (Cash Vouchers - Direct Manual Sales + Activated Batch Vouchers)
        $offlineRevenue = Transaction::where('transactions.status', 'success')
            ->where('transactions.payment_method', 'cash')
            ->leftJoin('wifi_users', 'transactions.wifi_user_id', '=', 'wifi_users.id')
            ->where(function($q) {
                $q->where('transactions.phone_number', '!=', 'Batch Print')
                  ->orWhereNotNull('wifi_users.activated_at');
            })
            ->sum('transactions.amount');

        $totalRevenue = $onlineRevenue + $offlineRevenue;
        $usedVouchersRevenue = $totalRevenue;
        
        // --- Revenue Today ---
        $onlineRevenueToday = Transaction::where('status', 'success')
            ->where('payment_method', '!=', 'cash')
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        $offlineRevenueToday = Transaction::where('transactions.status', 'success')
            ->where('transactions.payment_method', 'cash')
            ->leftJoin('wifi_users', 'transactions.wifi_user_id', '=', 'wifi_users.id')
            ->where(function($q) {
                $q->where(function($directQ) {
                    $directQ->where('transactions.phone_number', '!=', 'Batch Print')
                            ->whereDate('transactions.created_at', Carbon::today());
                })
                ->orWhere(function($batchQ) {
                    $batchQ->where('transactions.phone_number', 'Batch Print')
                           ->whereNotNull('wifi_users.activated_at')
                           ->whereDate('wifi_users.activated_at', Carbon::today());
                });
            })
            ->sum('transactions.amount');

        $revenueToday = $onlineRevenueToday + $offlineRevenueToday;

        // --- Revenue This Month ---
        $onlineRevenueMonth = Transaction::where('status', 'success')
            ->where('payment_method', '!=', 'cash')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('amount');

        $offlineRevenueMonth = Transaction::where('transactions.status', 'success')
            ->where('transactions.payment_method', 'cash')
            ->leftJoin('wifi_users', 'transactions.wifi_user_id', '=', 'wifi_users.id')
            ->where(function($q) {
                $q->where(function($directQ) {
                    $directQ->where('transactions.phone_number', '!=', 'Batch Print')
                            ->whereYear('transactions.created_at', Carbon::now()->year)
                            ->whereMonth('transactions.created_at', Carbon::now()->month);
                })
                ->orWhere(function($batchQ) {
                    $batchQ->where('transactions.phone_number', 'Batch Print')
                           ->whereNotNull('wifi_users.activated_at')
                           ->whereYear('wifi_users.activated_at', Carbon::now()->year)
                           ->whereMonth('wifi_users.activated_at', Carbon::now()->month);
                });
            })
            ->sum('transactions.amount');

        $revenueMonth = $onlineRevenueMonth + $offlineRevenueMonth;

        // --- Expenses & Net Deductions (Kupunguza Matumizi) ---
        $expensesToday = Expense::whereDate('expense_date', Carbon::today())->sum('amount');
        $cashExpensesToday = Expense::whereDate('expense_date', Carbon::today())
            ->where('payment_method', 'Cash')
            ->sum('amount');
        $totalExpenses = Expense::sum('amount');
        $totalCashExpenses = Expense::where('payment_method', 'Cash')->sum('amount');
        $expensesThisMonth = Expense::whereYear('expense_date', Carbon::now()->year)
            ->whereMonth('expense_date', Carbon::now()->month)
            ->sum('amount');
        $cashExpensesThisMonth = Expense::whereYear('expense_date', Carbon::now()->year)
            ->whereMonth('expense_date', Carbon::now()->month)
            ->where('payment_method', 'Cash')
            ->sum('amount');

        // Net Revenues (Pesa Halisi iliyobaki baada ya kupunguza matumizi yaliyosajiliwa)
        $netTotalRevenue = $totalRevenue - $totalExpenses;
        $netCashRevenue = max(0, $offlineRevenue - $totalCashExpenses);
        $netRevenueToday = $revenueToday - $expensesToday;
        $netOfflineRevenueToday = max(0, $offlineRevenueToday - $cashExpensesToday);
        $netRevenueMonth = $revenueMonth - $expensesThisMonth;
        $netOfflineRevenueMonth = max(0, $offlineRevenueMonth - $cashExpensesThisMonth);
        $netUsedVouchersRevenue = $netTotalRevenue;

        // 3. Counts
        // Number of vouchers that have actually been logged into Mikrotik
        $usedVouchersCount = WifiUser::whereNotNull('activated_at')->count();
        // Number of vouchers generated but never used
        $unusedVouchersCount = WifiUser::whereNull('activated_at')->count();

        // 4. Active Sessions (Hewani) & Active Subscribers (Wenye vifurushi vinavyoendelea)
        $activeSessions = WifiUser::where('is_online', true)->count();
        $activeSubscribers = WifiUser::whereNotNull('expires_at')
            ->where('expires_at', '>', Carbon::now())
            ->count();
        
        // 5. Router Stats
        $totalRouters = \App\Models\Router::count();
        
        // 6. Recent Transactions - Onyesha miamala 5 ya hivi karibuni (Online au vocha za cash/zilizotumika)
        $recentTransactions = Transaction::with(['package'])
            ->leftJoin('wifi_users', 'transactions.wifi_user_id', '=', 'wifi_users.id')
            ->where('transactions.status', 'success')
            ->where(function($q) {
                // Online sales OR direct manual cash sales OR activated batch print vouchers
                $q->where('transactions.phone_number', '!=', 'Batch Print')
                  ->orWhereNotNull('wifi_users.activated_at');
            })
            ->select('transactions.*')
            ->orderBy(\Illuminate\Support\Facades\DB::raw('COALESCE(wifi_users.activated_at, transactions.created_at)'), 'desc')
            ->take(5)
            ->get();

        $packages = \App\Models\Package::where('is_active', true)->get();

        // 7. Total Data Usage
        $totalBytes = WifiUser::sum('data_used');
        if ($totalBytes >= (1024 * 1024 * 1024)) {
            $totalDataUsage = number_format($totalBytes / (1024 * 1024 * 1024), 2) . ' GB';
        } else {
            $totalDataUsage = number_format($totalBytes / (1024 * 1024), 2) . ' MB';
        }

        // 8. SaaS Limits
        $tenantContext = app(\App\Services\TenantContext::class);
        $tenant = $tenantContext->getTenant();
        
        $saasPlan = null;
        $maxRouters = 0;
        $maxCustomers = 0;
        
        if ($tenant) {
            $subscription = $tenant->currentSubscription;
            if ($subscription && $subscription->plan) {
                $saasPlan = $subscription->plan->name;
                $maxRouters = $subscription->plan->max_routers;
                $maxCustomers = $subscription->plan->max_customers;
            }
        }
        
        $totalCustomers = \App\Models\Customer::count();

        // 9. 7-Day Revenue Trend for Dashboard Chart (Online created_at + Direct cash created_at + Batch activated_at)
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);

            // Online sales for that day
            $dayOnline = Transaction::where('status', 'success')
                ->where('payment_method', '!=', 'cash')
                ->whereNotIn('phone_number', ['Batch Print', 'Mteja wa Cash', 'Mauzo ya Vocha (Cash)'])
                ->whereDate('created_at', $date)
                ->sum('amount');

            // Direct cash sales on that day (not batch print)
            $dayDirectCash = Transaction::where('status', 'success')
                ->where('payment_method', 'cash')
                ->where('phone_number', '!=', 'Batch Print')
                ->whereDate('created_at', $date)
                ->sum('amount');

            // Activated batch print vouchers on that day
            $dayBatchCash = Transaction::where('transactions.status', 'success')
                ->where('transactions.phone_number', 'Batch Print')
                ->join('wifi_users', function($join) {
                    $join->on('transactions.wifi_user_id', '=', 'wifi_users.id')
                         ->orOn('transactions.mikrotik_voucher', '=', 'wifi_users.mikrotik_username');
                })
                ->whereNotNull('wifi_users.activated_at')
                ->whereDate('wifi_users.activated_at', $date)
                ->sum('transactions.amount');

            $chartLabels[] = $date->format('D');
            $chartData[] = (float) ($dayOnline + $dayDirectCash + $dayBatchCash);
        }

        $totalWeekSales = array_sum($chartData);

        return view('admin.dashboard', compact(
            'totalWeekSales',
            'usedVouchersRevenue',
            'totalRevenue',
            'onlineRevenue',
            'offlineRevenue',
            'netTotalRevenue',
            'netCashRevenue',
            'revenueToday',
            'onlineRevenueToday',
            'offlineRevenueToday',
            'netRevenueToday',
            'netOfflineRevenueToday',
            'revenueMonth',
            'onlineRevenueMonth',
            'offlineRevenueMonth',
            'netRevenueMonth',
            'netOfflineRevenueMonth',
            'expensesToday',
            'cashExpensesToday',
            'totalExpenses',
            'expensesThisMonth',
            'cashExpensesThisMonth',
            'netUsedVouchersRevenue',
            'usedVouchersCount',
            'unusedVouchersCount',
            'activeSessions',
            'activeSubscribers',
            'totalRouters',
            'recentTransactions',
            'packages',
            'totalDataUsage',
            'saasPlan',
            'maxRouters',
            'maxCustomers',
            'totalCustomers',
            'chartLabels',
            'chartData'
        ));
    }

    /**
     * JSON endpoint ya dashboard - inarudisha miamala 5 ya hivi karibuni kwa AJAX.
     * Inaitwa kila sekunde 15 na JavaScript bila kuload ukurasa wote.
     */
    public function recentTransactionsJson()
    {
        $transactions = Transaction::with(['package'])
            ->leftJoin('wifi_users', 'transactions.wifi_user_id', '=', 'wifi_users.id')
            ->where('transactions.status', 'success')
            ->where(function($q) {
                $q->where('transactions.phone_number', '!=', 'Batch Print')
                  ->orWhereNotNull('wifi_users.activated_at');
            })
            ->select('transactions.*')
            ->orderBy(\Illuminate\Support\Facades\DB::raw('COALESCE(wifi_users.activated_at, transactions.created_at)'), 'desc')
            ->take(5)
            ->get()
            ->map(function ($txn) {
                return [
                    'id'                  => $txn->id,
                    'phone_number'        => $txn->phone_number,
                    'payment_method'      => $txn->payment_method,
                    'status'              => $txn->status,
                    'amount'              => $txn->amount,
                    'amount_formatted'    => number_format($txn->amount),
                    'mikrotik_voucher'    => $txn->mikrotik_voucher,
                    'package_name'        => $txn->package ? $txn->package->name : null,
                    'created_at_formatted'=> $txn->created_at->format('d M, H:i'),
                    'created_at_human'    => $txn->created_at->diffForHumans(),
                ];
            });

        return response()->json(['transactions' => $transactions]);
    }

    public function checkNotifications()
    {
        $notifications = [];

        // 1. Check for new successful direct transactions (online or manual cash)
        $onlineTransactions = Transaction::with('package')
            ->where('status', 'success')
            ->where('is_notified', false)
            ->where('phone_number', '!=', 'Batch Print')
            ->get();

        // 2. Check for newly ACTIVATED batch print vouchers
        $offlineTransactions = Transaction::with('package')
            ->where('transactions.status', 'success')
            ->where('transactions.is_notified', false)
            ->where('transactions.phone_number', 'Batch Print')
            ->join('wifi_users', 'transactions.wifi_user_id', '=', 'wifi_users.id')
            ->whereNotNull('wifi_users.activated_at')
            ->select('transactions.*') // prevent id conflict
            ->get();

        $newTransactions = $onlineTransactions->merge($offlineTransactions);

        foreach ($newTransactions as $txn) {
            $package = $txn->package ? $txn->package->name : 'Kifurushi';
            $amount = number_format($txn->amount);
            $targetLink = route('admin.transactions', [
                'highlight' => $txn->id,
                'search'    => $txn->transaction_reference ?: $txn->phone_number,
            ]);
            
            $isCashOrBatch = ($txn->payment_method === 'cash' || $txn->phone_number === 'Batch Print' || $txn->phone_number === 'Mteja wa Cash' || $txn->phone_number === 'Mauzo ya Vocha (Cash)');
            if ($isCashOrBatch) {
                $isBatch = ($txn->phone_number === 'Batch Print');
                $title = $isBatch ? "Vocha (Karatasi): {$package}" : "Mauzo ya Vocha (Cash): {$package}";
                $msg = $isBatch 
                    ? "Vocha ya karatasi imeingizwa: {$package} (Tsh {$amount}) mtandaoni."
                    : "Vocha ya cash imeuuzwa: {$package} (Tsh {$amount}).";
                $appNotif = \App\Models\AppNotification::create([
                    'type'    => 'voucher',
                    'title'   => $title,
                    'message' => $msg,
                    'link'    => $targetLink,
                    'icon'    => 'fas fa-ticket-alt',
                    'color'   => '#D97706',
                    'data'    => ['transaction_id' => $txn->id],
                    'is_read' => false,
                ]);
                $notifications[] = [
                    'id'      => $appNotif->id,
                    'type'    => 'voucher',
                    'message' => $msg,
                    'link'    => route('admin.notifications.go', $appNotif->id),
                ];
            } else {
                $msg = "Malipo: {$txn->phone_number} amelipia Tsh {$amount}";
                $appNotif = \App\Models\AppNotification::create([
                    'type'    => 'payment',
                    'title'   => "Malipo: Tsh {$amount}",
                    'message' => "Mteja {$txn->phone_number} amelipia kifurushi cha {$package}.",
                    'link'    => $targetLink,
                    'icon'    => 'fas fa-mobile-alt',
                    'color'   => '#10B981',
                    'data'    => ['transaction_id' => $txn->id],
                    'is_read' => false,
                ]);
                $notifications[] = [
                    'id'      => $appNotif->id,
                    'type'    => 'payment',
                    'message' => $msg,
                    'link'    => route('admin.notifications.go', $appNotif->id),
                ];
            }
            
            // Mark as notified
            $txn->update(['is_notified' => true]);
        }

        // 2. Check if Router is offline (no sync for > 2 minutes)
        $tenantId = app(\App\Services\TenantContext::class)->getTenantId();
        $lastSync = \Illuminate\Support\Facades\Cache::get('mikrotik_last_sync_' . $tenantId);
        $offlineNotified = \Illuminate\Support\Facades\Cache::get('mikrotik_offline_notified');
        
        $isOffline = false;
        if (!$lastSync || now()->diffInSeconds($lastSync) > 120) {
            $isOffline = true;
        }

        if ($isOffline && !$offlineNotified) {
            $msg = "Onyo: Router imeacha kusoma! Hakikisha ina umeme na intaneti.";
            $appNotif = \App\Models\AppNotification::create([
                'type'    => 'alert',
                'title'   => 'Router Ipo Chini (Offline)',
                'message' => $msg,
                'link'    => route('admin.routers.index'),
                'icon'    => 'fas fa-exclamation-triangle',
                'color'   => '#EF4444',
                'is_read' => false,
            ]);
            $notifications[] = [
                'id'      => $appNotif->id,
                'type'    => 'alert',
                'message' => $msg,
                'link'    => route('admin.notifications.go', $appNotif->id),
            ];
            \Illuminate\Support\Facades\Cache::put('mikrotik_offline_notified', true, 86400); // remind again in 24h if still offline
        } elseif (!$isOffline && $offlineNotified) {
            // Router came back online, clear the flag
            \Illuminate\Support\Facades\Cache::forget('mikrotik_offline_notified');
            $msg = "Safi: Router imerudi hewani!";
            $appNotif = \App\Models\AppNotification::create([
                'type'    => 'router',
                'title'   => 'Router Imerudi Hewani',
                'message' => $msg,
                'link'    => route('admin.routers.index'),
                'icon'    => 'fas fa-check-circle',
                'color'   => '#10B981',
                'is_read' => false,
            ]);
            $notifications[] = [
                'id'      => $appNotif->id,
                'type'    => 'success',
                'message' => $msg,
                'link'    => route('admin.notifications.go', $appNotif->id),
            ];
        }

        return response()->json([
            'notifications' => $notifications
        ]);
    }
}
