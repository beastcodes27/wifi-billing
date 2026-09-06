<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Site;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses with KPI summaries and filters.
     */
    public function index(Request $request)
    {
        $query = Expense::with(['site', 'recordedBy']);

        // Filter by Search Query
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Filter by Category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by Payment Method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        // Quick Period Filter (Leo, Wiki Hii, Mwezi Huu)
        if ($request->filled('period')) {
            if ($request->period === 'today') {
                $query->whereDate('expense_date', Carbon::today());
            } elseif ($request->period === 'week') {
                $query->whereBetween('expense_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } elseif ($request->period === 'month') {
                $query->whereYear('expense_date', Carbon::now()->year)
                      ->whereMonth('expense_date', Carbon::now()->month);
            }
        }

        $expenses = $query->orderBy('expense_date', 'desc')
                          ->orderBy('id', 'desc')
                          ->paginate(15)
                          ->withQueryString();

        // Financial KPIs
        $expensesToday = Expense::whereDate('expense_date', Carbon::today())->sum('amount');
        $expensesThisMonth = Expense::whereYear('expense_date', Carbon::now()->year)
                                    ->whereMonth('expense_date', Carbon::now()->month)
                                    ->sum('amount');
        $totalExpenses = Expense::sum('amount');
        $expensesCount = Expense::count();

        // Category breakdown for summary pills
        $categoryBreakdown = Expense::selectRaw('category, SUM(amount) as total_amount, COUNT(*) as count')
                                    ->groupBy('category')
                                    ->orderBy('total_amount', 'desc')
                                    ->take(6)
                                    ->get();

        $sites = Site::orderBy('name')->get();
        $categories = Expense::categories();
        $paymentMethods = Expense::paymentMethods();

        return view('admin.expenses.index', compact(
            'expenses',
            'expensesToday',
            'expensesThisMonth',
            'totalExpenses',
            'expensesCount',
            'categoryBreakdown',
            'sites',
            'categories',
            'paymentMethods'
        ));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0.01',
            'category'       => 'required|string|max:100',
            'expense_date'   => 'required|date',
            'payment_method' => 'required|string|max:100',
            'reference_no'   => 'nullable|string|max:255',
            'site_id'        => 'nullable|exists:sites,id',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $validated['recorded_by'] = auth()->id();

        $expense = Expense::create($validated);

        return redirect()->route('admin.expenses.index')
                         ->with('success', 'Matumizi ya Tsh ' . number_format($expense->amount, 2) . ' yamesajiliwa kikamilifu.');
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0.01',
            'category'       => 'required|string|max:100',
            'expense_date'   => 'required|date',
            'payment_method' => 'required|string|max:100',
            'reference_no'   => 'nullable|string|max:255',
            'site_id'        => 'nullable|exists:sites,id',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $expense->update($validated);

        return redirect()->route('admin.expenses.index')
                         ->with('success', 'Matumizi yamehaririwa kikamilifu.');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Expense $expense)
    {
        $amount = $expense->amount;
        $expense->delete();

        return redirect()->route('admin.expenses.index')
                         ->with('success', 'Matumizi ya Tsh ' . number_format($amount, 2) . ' yamefutwa kikamilifu.');
    }
}
