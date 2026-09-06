<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Package;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Revenue over the last 30 days
        $thirtyDaysAgo = Carbon::now()->subDays(29)->startOfDay();
        
        $dailyOnline = Transaction::where('status', 'success')
            ->where('payment_method', '!=', 'cash')
            ->whereNotIn('phone_number', ['Batch Print', 'Mteja wa Cash', 'Mauzo ya Vocha (Cash)'])
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $dailyDirectCash = Transaction::where('status', 'success')
            ->where('payment_method', 'cash')
            ->where('phone_number', '!=', 'Batch Print')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $dailyBatchCash = Transaction::where('transactions.status', 'success')
            ->where('transactions.phone_number', 'Batch Print')
            ->join('wifi_users', function($join) {
                $join->on('transactions.wifi_user_id', '=', 'wifi_users.id')
                     ->orOn('transactions.mikrotik_voucher', '=', 'wifi_users.mikrotik_username');
            })
            ->whereNotNull('wifi_users.activated_at')
            ->where('wifi_users.activated_at', '>=', $thirtyDaysAgo)
            ->select(
                DB::raw('DATE(wifi_users.activated_at) as date'),
                DB::raw('SUM(transactions.amount) as total')
            )
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $chartLabels = [];
        $chartData = [];
        
        // Fill missing days with 0
        $currentDate = $thirtyDaysAgo->copy();
        while ($currentDate <= Carbon::now()) {
            $dateString = $currentDate->format('Y-m-d');
            $chartLabels[] = $currentDate->format('d M');
            
            $onlineTot = isset($dailyOnline[$dateString]) ? $dailyOnline[$dateString]->total : 0;
            $directCashTot = isset($dailyDirectCash[$dateString]) ? $dailyDirectCash[$dateString]->total : 0;
            $batchCashTot = isset($dailyBatchCash[$dateString]) ? $dailyBatchCash[$dateString]->total : 0;
            $chartData[] = (float) ($onlineTot + $directCashTot + $batchCashTot);
            
            $currentDate->addDay();
        }

        // 2. Package Popularity (All time successful online & activated vouchers / direct cash)
        $packageStats = Transaction::where('transactions.status', 'success')
            ->leftJoin('wifi_users', 'transactions.wifi_user_id', '=', 'wifi_users.id')
            ->where(function ($q) {
                $q->where('transactions.phone_number', '!=', 'Batch Print')
                  ->orWhereNotNull('wifi_users.activated_at');
            })
            ->select('transactions.package_id', DB::raw('COUNT(transactions.id) as count'), DB::raw('SUM(transactions.amount) as total_revenue'))
            ->groupBy('transactions.package_id')
            ->with('package')
            ->get()
            ->sortByDesc('count');

        // 3. Key Metrics
        $totalCustomers = Customer::count();
        
        $onlineRevenue = Transaction::where('status', 'success')
            ->where('payment_method', '!=', 'cash')
            ->whereNotIn('phone_number', ['Batch Print', 'Mteja wa Cash', 'Mauzo ya Vocha (Cash)'])
            ->sum('amount');
            
        $directCashTotal = Transaction::where('status', 'success')
            ->where('payment_method', 'cash')
            ->where('phone_number', '!=', 'Batch Print')
            ->sum('amount');

        $batchCashTotal = Transaction::where('transactions.status', 'success')
            ->where('transactions.phone_number', 'Batch Print')
            ->join('wifi_users', function($join) {
                $join->on('transactions.wifi_user_id', '=', 'wifi_users.id')
                     ->orOn('transactions.mikrotik_voucher', '=', 'wifi_users.mikrotik_username');
            })
            ->whereNotNull('wifi_users.activated_at')
            ->sum('transactions.amount');

        $offlineRevenue = $directCashTotal + $batchCashTotal;
        $totalRevenue = $onlineRevenue + $offlineRevenue;

        $thisMonthOnline = Transaction::where('status', 'success')
            ->where('payment_method', '!=', 'cash')
            ->whereNotIn('phone_number', ['Batch Print', 'Mteja wa Cash', 'Mauzo ya Vocha (Cash)'])
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        $thisMonthDirectCash = Transaction::where('status', 'success')
            ->where('payment_method', 'cash')
            ->where('phone_number', '!=', 'Batch Print')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        $thisMonthBatchCash = Transaction::where('transactions.status', 'success')
            ->where('transactions.phone_number', 'Batch Print')
            ->join('wifi_users', function($join) {
                $join->on('transactions.wifi_user_id', '=', 'wifi_users.id')
                     ->orOn('transactions.mikrotik_voucher', '=', 'wifi_users.mikrotik_username');
            })
            ->whereNotNull('wifi_users.activated_at')
            ->whereMonth('wifi_users.activated_at', Carbon::now()->month)
            ->whereYear('wifi_users.activated_at', Carbon::now()->year)
            ->sum('transactions.amount');

        $thisMonthOffline = $thisMonthDirectCash + $thisMonthBatchCash;
        $thisMonthRevenue = $thisMonthOnline + $thisMonthOffline;
        
        $paymentMethods = Transaction::where('transactions.status', 'success')
            ->leftJoin('wifi_users', 'transactions.wifi_user_id', '=', 'wifi_users.id')
            ->where(function ($q) {
                $q->where('transactions.phone_number', '!=', 'Batch Print')
                  ->orWhereNotNull('wifi_users.activated_at');
            })
            ->select('transactions.payment_method', DB::raw('COUNT(transactions.id) as count'))
            ->groupBy('transactions.payment_method')
            ->get();

        $totalExpenses = \App\Models\Expense::sum('amount');
        $thisMonthExpenses = \App\Models\Expense::whereYear('expense_date', Carbon::now()->year)
            ->whereMonth('expense_date', Carbon::now()->month)
            ->sum('amount');
        $netProfitTotal = $totalRevenue - $totalExpenses;
        $netProfitThisMonth = $thisMonthRevenue - $thisMonthExpenses;

        return view('admin.reports.index', compact(
            'chartLabels', 
            'chartData', 
            'packageStats', 
            'totalCustomers', 
            'totalRevenue', 
            'thisMonthRevenue', 
            'onlineRevenue', 
            'offlineRevenue', 
            'thisMonthOnline', 
            'thisMonthOffline', 
            'paymentMethods', 
            'totalExpenses', 
            'thisMonthExpenses', 
            'netProfitTotal', 
            'netProfitThisMonth'
        ));
    }
}
