<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppNotification;
use App\Models\Transaction;
use App\Models\WifiUser;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureRecentNotificationsExist();

        // If requested to mark all read (e.g. when opening notification center from bell)
        if ($request->boolean('mark_read')) {
            AppNotification::where('is_read', false)->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        $filter = $request->query('filter', 'all');
        $search = $request->query('search');

        $query = AppNotification::orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($filter === 'unread') {
            $query->where('is_read', false);
        } elseif ($filter === 'payment') {
            $query->where('type', 'payment');
        } elseif ($filter === 'voucher') {
            $query->where('type', 'voucher');
        } elseif ($filter === 'router') {
            $query->whereIn('type', ['router', 'alert']);
        }

        $notifications = $query->paginate(20)->withQueryString();

        $stats = [
            'total'   => AppNotification::count(),
            'unread'  => AppNotification::where('is_read', false)->count(),
            'payment' => AppNotification::where('type', 'payment')->count(),
            'voucher' => AppNotification::where('type', 'voucher')->count(),
            'router'  => AppNotification::whereIn('type', ['router', 'alert'])->count(),
        ];

        return view('admin.notifications.index', compact('notifications', 'stats', 'filter', 'search'));
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = AppNotification::findOrFail($id);
        $notification->markAsRead();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => AppNotification::where('is_read', false)->count()
            ]);
        }

        return back()->with('success', 'Taarifa imetiwa alama kuwa imesomwa.');
    }

    public function markAllAsRead(Request $request)
    {
        AppNotification::where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'unread_count' => 0]);
        }

        return back()->with('success', 'Taarifa zote zimetiwa alama kuwa zimesomwa.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids) && is_array($ids)) {
            AppNotification::whereIn('id', $ids)->delete();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => AppNotification::where('is_read', false)->count()
            ]);
        }

        return back()->with('success', count($ids) . ' taarifa zimefutwa kikamilifu.');
    }

    public function bulkRead(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids) && is_array($ids)) {
            AppNotification::whereIn('id', $ids)->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => AppNotification::where('is_read', false)->count()
            ]);
        }

        return back()->with('success', count($ids) . ' taarifa zimetiwa alama kuwa zimesomwa.');
    }

    public function destroy(Request $request, $id)
    {
        $notification = AppNotification::findOrFail($id);
        $notification->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => AppNotification::where('is_read', false)->count()
            ]);
        }

        return back()->with('success', 'Taarifa imefutwa kikamilifu.');
    }

    public function clearAll(Request $request)
    {
        AppNotification::query()->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'unread_count' => 0]);
        }

        return back()->with('success', 'Taarifa zote zimefutwa.');
    }

    public function goToNotification(Request $request, $id)
    {
        $notification = AppNotification::findOrFail($id);

        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        $link = $notification->link;

        // If link already points to a specific transaction highlight or search, use it
        if ($link && (str_contains($link, 'highlight=') || str_contains($link, 'search='))) {
            return redirect($link);
        }

        // For payment and voucher notifications, find the exact transaction
        if ($notification->type === 'payment' || $notification->type === 'voucher') {
            $txn = null;

            if (!empty($notification->data['transaction_id'])) {
                $txn = Transaction::find($notification->data['transaction_id']);
            }

            // Extract phone number from message
            if (!$txn && preg_match('/(0[67]\d{8}|255[67]\d{8})/', $notification->message, $matches)) {
                $phone = $matches[1];
                $txn = Transaction::where('phone_number', $phone)->latest()->first();
            }

            // Extract 6-digit voucher code from message
            if (!$txn && preg_match('/\b(\d{6})\b/', $notification->message, $matches)) {
                $code = $matches[1];
                $txn = Transaction::where('mikrotik_voucher', $code)->latest()->first();
            }

            // Fallback: match closest transaction by timestamp
            if (!$txn) {
                $txn = Transaction::where('status', 'success')
                    ->whereBetween('created_at', [
                        $notification->created_at->copy()->subMinutes(10),
                        $notification->created_at->copy()->addMinutes(10),
                    ])
                    ->latest()
                    ->first();
            }

            if ($txn) {
                return redirect()->route('admin.transactions', [
                    'highlight' => $txn->id,
                    'search'    => $txn->transaction_reference ?: $txn->phone_number,
                ]);
            }

            return redirect()->route('admin.transactions');
        }

        if ($link) {
            return redirect($link);
        }

        return redirect()->route('admin.notifications.index');
    }

    public function latestJson()
    {
        $this->ensureRecentNotificationsExist();

        $unreadCount = AppNotification::where('is_read', false)->count();
        $latest = AppNotification::orderBy('created_at', 'desc')
            ->take(8)
            ->get()
            ->map(function ($n) {
                return [
                    'id'         => $n->id,
                    'type'       => $n->type,
                    'title'      => $n->title,
                    'message'    => $n->message,
                    'link'       => route('admin.notifications.go', $n->id),
                    'icon'       => $n->icon,
                    'color'      => $n->color,
                    'is_read'    => $n->is_read,
                    'time_human' => $n->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $latest,
        ]);
    }

    /**
     * Backfill recent transactions as notifications if table is empty so Notification Center is immediately useful
     */
    private function ensureRecentNotificationsExist()
    {
        if (AppNotification::count() > 0) {
            return;
        }

        // Seed up to 15 recent transactions as initial notifications (Online au vocha zilizotumika pekee)
        $recentTransactions = Transaction::with(['package'])
            ->leftJoin('wifi_users', 'transactions.wifi_user_id', '=', 'wifi_users.id')
            ->where('transactions.status', 'success')
            ->where(function($q) {
                $q->where(function($onlineQ) {
                    $onlineQ->where('transactions.payment_method', '!=', 'cash')
                            ->whereNotIn('transactions.phone_number', ['Batch Print', 'Mteja wa Cash']);
                })
                ->orWhereNotNull('wifi_users.activated_at');
            })
            ->select('transactions.*')
            ->orderBy(\Illuminate\Support\Facades\DB::raw('COALESCE(wifi_users.activated_at, transactions.created_at)'), 'desc')
            ->take(15)
            ->get();

        foreach ($recentTransactions as $txn) {
            $isCash = in_array($txn->phone_number, ['Batch Print', 'Mteja wa Cash']);
            $pkgName = $txn->package ? $txn->package->name : 'WiFi Kifurushi';
            $amount = number_format($txn->amount);
            $targetLink = route('admin.transactions', [
                'highlight' => $txn->id,
                'search'    => $txn->transaction_reference ?: $txn->phone_number,
            ]);

            if ($isCash) {
                AppNotification::create([
                    'type'       => 'voucher',
                    'title'      => "Vocha Imetumika ({$pkgName})",
                    'message'    => "Vocha ya Tsh {$amount} (" . ($txn->mikrotik_voucher ?: 'Vocha') . ") imetumika mtandaoni.",
                    'link'       => $targetLink,
                    'icon'       => 'fas fa-ticket-alt',
                    'color'      => '#D97706',
                    'data'       => ['transaction_id' => $txn->id],
                    'is_read'    => true,
                    'read_at'    => $txn->created_at,
                    'created_at' => $txn->created_at,
                    'updated_at' => $txn->created_at,
                ]);
            } else {
                AppNotification::create([
                    'type'       => 'payment',
                    'title'      => "Malipo Mapya (Tsh {$amount})",
                    'message'    => "Mteja {$txn->phone_number} amelipia kifurushi cha {$pkgName}.",
                    'link'       => $targetLink,
                    'icon'       => 'fas fa-mobile-alt',
                    'color'      => '#10B981',
                    'data'       => ['transaction_id' => $txn->id],
                    'is_read'    => true,
                    'read_at'    => $txn->created_at,
                    'created_at' => $txn->created_at,
                    'updated_at' => $txn->created_at,
                ]);
            }
        }
    }
}
