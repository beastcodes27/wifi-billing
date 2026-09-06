<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Router;
use App\Models\Site;
use App\Models\WifiUser;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Customer::withCount(['wifiUsers', 'transactions'])
            ->with(['activeWifiUser.package']);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });

            // Also search by wifi username or MAC
            $wifiUserIds = WifiUser::where('mikrotik_username', 'like', "%{$search}%")
                ->orWhere('mac_address', 'like', "%{$search}%")
                ->pluck('customer_id')
                ->filter()
                ->unique();

            if ($wifiUserIds->isNotEmpty()) {
                $query->orWhereIn('id', $wifiUserIds);
            }
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Sort
        $sort    = $request->input('sort', 'created_at');
        $dir     = $request->input('dir', 'desc');
        $allowed = ['name', 'phone_number', 'status', 'created_at'];
        if (in_array($sort, $allowed)) {
            $query->orderBy($sort, $dir === 'asc' ? 'asc' : 'desc');
        }

        $customers = $query->paginate(20)->withQueryString();

        $stats = [
            'total'     => Customer::count(),
            'active'    => Customer::where('status', 'active')->count(),
            'expired'   => Customer::where('status', 'expired')->count(),
            'suspended' => Customer::where('status', 'suspended')->count(),
            'online'    => WifiUser::where('is_online', true)
                               ->whereNotNull('customer_id')->distinct('customer_id')->count(),
        ];

        return view('admin.customers.index', compact('customers', 'stats', 'search', 'status', 'sort', 'dir'));
    }

    // ─── Create ───────────────────────────────────────────────────────────────

    public function create()
    {
        $packages = Package::where('is_active', true)->orderBy('price')->get();
        $routers  = Router::orderBy('name')->get();
        $sites    = Site::orderBy('name')->get();

        return view('admin.customers.create', compact('packages', 'routers', 'sites'));
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:191',
            'phone_number' => 'required|string|max:20',
            'email'        => 'nullable|email|max:191',
            'notes'        => 'nullable|string|max:500',
        ]);

        $customer = Customer::create([
            'name'         => $request->name,
            'phone_number' => $request->phone_number,
            'email'        => $request->email,
            'status'       => 'active',
            'notes'        => $request->notes,
            'created_by'   => auth()->id(),
        ]);

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', "Customer {$customer->name} ameundwa kikamilifu!");
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function show(Customer $customer)
    {
        $customer->load([
            'wifiUsers.package',
            'wifiUsers.router',
            'wifiUsers.site',
            'transactions.package',
            'subscriptions.package',
            'subscriptions.transaction',
            'ledgers.creator',
        ]);

        $stats = [
            'total_spent'    => $customer->transactions()->where('status', 'success')->sum('amount'),
            'total_sessions' => $customer->wifiUsers()->whereNotNull('activated_at')->count(),
            'data_used_mb'   => $customer->wifiUsers()->sum('data_used') / (1024 * 1024),
            'last_seen'      => $customer->wifiUsers()->whereNotNull('last_logout_at')
                                    ->max('last_logout_at'),
            'is_online'      => $customer->wifiUsers()->where('is_online', true)->exists(),
        ];

        $packages = Package::where('is_active', true)->orderBy('price')->get();

        return view('admin.customers.show', compact('customer', 'stats', 'packages'));
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'         => 'required|string|max:191',
            'phone_number' => 'required|string|max:20',
            'email'        => 'nullable|email|max:191',
            'status'       => 'required|in:active,expired,suspended,blocked',
            'notes'        => 'nullable|string|max:500',
        ]);

        $customer->update([
            'name'         => $request->name,
            'phone_number' => $request->phone_number,
            'email'        => $request->email,
            'status'       => $request->status,
            'notes'        => $request->notes,
        ]);

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Customer amesasishwa kikamilifu.');
    }

    // ─── Suspend ──────────────────────────────────────────────────────────────

    public function suspend(Customer $customer)
    {
        $customer->update(['status' => 'suspended']);

        return back()->with('success', "Customer {$customer->name} amesimamishwa.");
    }

    // ─── Activate ─────────────────────────────────────────────────────────────

    public function activate(Customer $customer)
    {
        $customer->update(['status' => 'active']);

        return back()->with('success', "Customer {$customer->name} ameamilishwa.");
    }

    // ─── Delete ───────────────────────────────────────────────────────────────

    public function destroy(Customer $customer)
    {
        // Soft delete — data remains for reporting
        $customer->delete();

        return redirect()
            ->route('admin.customers.index')
            ->with('success', "Customer {$customer->name} amefutwa.");
    }

    // ─── Ledger / Debt ────────────────────────────────────────────────────────

    public function addDebt(Request $request, Customer $customer)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $customer->ledgers()->create([
            'type' => 'debt',
            'amount' => $request->amount,
            'description' => $request->description ?: 'Deni la Vocha',
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Deni limesajiliwa kikamilifu.');
    }

    public function addPayment(Request $request, Customer $customer)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $customer->ledgers()->create([
            'type' => 'payment',
            'amount' => $request->amount,
            'description' => $request->description ?: 'Malipo ya Awamu',
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Malipo yamesajiliwa kikamilifu.');
    }

    // ─── Global Search (JSON) ─────────────────────────────────────────────────

    public function search(Request $request)
    {
        $term = $request->input('q', '');

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $customers = Customer::with(['activeWifiUser.package'])
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('phone_number', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%");
            })
            ->orWhereHas('wifiUsers', function ($q) use ($term) {
                $q->where('mikrotik_username', 'like', "%{$term}%")
                  ->orWhere('mac_address', 'like', "%{$term}%");
            })
            ->limit(8)
            ->get();

        // Also search transactions for customers who paid
        $txnCustomerIds = Transaction::where('transaction_reference', 'like', "%{$term}%")
            ->orWhere('mikrotik_voucher', 'like', "%{$term}%")
            ->whereNotNull('customer_id')
            ->pluck('customer_id')
            ->unique();

        if ($txnCustomerIds->isNotEmpty()) {
            $extra = Customer::whereIn('id', $txnCustomerIds)
                ->whereNotIn('id', $customers->pluck('id'))
                ->limit(4)
                ->get();
            $customers = $customers->merge($extra);
        }

        return response()->json($customers->map(function ($c) {
            return [
                'id'           => $c->id,
                'name'         => $c->name,
                'phone_number' => $c->phone_number,
                'status'       => $c->status,
                'package'      => optional(optional($c->activeWifiUser)->package)->name,
                'url'          => route('admin.customers.show', $c),
            ];
        }));
    }
}
