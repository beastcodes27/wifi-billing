<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with(['customer', 'package', 'wifiUser', 'transaction']);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->whereHas('customer', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%");
                })->orWhereHas('wifiUser', function($q) use ($search) {
                    $q->where('mikrotik_username', 'like', "%{$search}%");
                })->orWhereHas('transaction', function($q) use ($search) {
                    $q->where('transaction_reference', 'like', "%{$search}%");
                });
            });
        }

        // Status Filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Type Filter
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $stats = [
            'total' => Subscription::count(),
            'active' => Subscription::where('status', 'active')->count(),
            'expired' => Subscription::where('status', 'expired')->count(),
            'cancelled' => Subscription::where('status', 'cancelled')->count(),
        ];

        return view('admin.subscriptions.index', compact('subscriptions', 'stats', 'search', 'status', 'type'));
    }

    public function cancel(Subscription $subscription)
    {
        $subscription->update([
            'status' => 'cancelled',
            'notes' => trim($subscription->notes . "\nCancelled by admin: " . auth()->user()->name)
        ]);

        return back()->with('success', 'Subscription imesitishwa kikamilifu.');
    }
}
