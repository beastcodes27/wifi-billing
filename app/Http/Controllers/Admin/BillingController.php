<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaasPlan;
use App\Models\SaasSubscription;
use App\Services\SonicPesaService;
use App\Services\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{
    protected SonicPesaService $sonic;

    public function __construct(SonicPesaService $sonic)
    {
        $this->sonic = $sonic;
    }

    public function index()
    {
        $plans = SaasPlan::where('is_active', true)->orderBy('price')->get();

        $tenant = app(TenantContext::class)->getTenant();
        $subscription = $tenant
            ? ($tenant->activeSubscription() ?: $tenant->currentSubscription)
            : null;

        return view('admin.billing', compact('plans', 'subscription'));
    }

    public function pay(Request $request)
    {
        $tenant = app(TenantContext::class)->getTenant();

        if (! $tenant) {
            return back()->with('error', 'Biashara haijatambulika.');
        }

        $validated = $request->validate([
            'phone' => 'required|string',
            'plan_id' => 'required|exists:saas_plans,id',
        ]);

        $plan = SaasPlan::findOrFail($validated['plan_id']);
        $amount = $plan->price;

        $current = $tenant->activeSubscription() ?: $tenant->currentSubscription;
        $base = ($current && $current->ends_at && $current->ends_at->isFuture())
            ? $current->ends_at->copy()
            : now();

        // Free plan — activate immediately without a gateway round-trip.
        if ($amount <= 0) {
            SaasSubscription::create([
                'tenant_id' => $tenant->id,
                'saas_plan_id' => $plan->id,
                'amount_paid' => 0,
                'payment_method' => 'free',
                'reference_number' => 'FREE-' . strtoupper(bin2hex(random_bytes(4))),
                'starts_at' => now(),
                'ends_at' => $base->addMonths($plan->billing_cycle_months),
                'status' => 'active',
            ]);
            $tenant->update(['status' => 'active']);

            return redirect()->route('admin.dashboard')->with('success', 'Umefanikiwa kujiunga na kifurushi cha bure!');
        }

        $response = $this->sonic->createOrder([
            'phone' => $validated['phone'],
            'amount' => $amount,
            'buyer_name' => $tenant->owner_name ?: $tenant->business_name,
            'buyer_email' => $tenant->email ?: 'noreply@medbando.app',
        ]);

        $orderId = $this->sonic->extractOrderId($response);

        if (! $orderId) {
            Log::error('SaaS SonicPesa initiation failed', ['response' => $response]);

            return back()->with('error', 'Kuna tatizo kuanzisha malipo. Hakikisha namba ya simu ipo sahihi na ina salio.');
        }

        $subscription = SaasSubscription::create([
            'tenant_id' => $tenant->id,
            'saas_plan_id' => $plan->id,
            'amount_paid' => $amount,
            'payment_method' => 'sonicpesa',
            'reference_number' => $orderId,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.billing.processing', $subscription->id);
    }

    public function processing($id)
    {
        $tenant = app(TenantContext::class)->getTenant();
        $subscription = SaasSubscription::where('tenant_id', $tenant->id)->findOrFail($id);

        if ($subscription->status === 'active') {
            return redirect()->route('admin.dashboard')->with('success', 'Malipo yamefanikiwa! Kifurushi chako kipo hewani.');
        }

        return view('admin.billing_processing', compact('subscription'));
    }

    public function status($id)
    {
        $tenant = app(TenantContext::class)->getTenant();
        $subscription = SaasSubscription::where('tenant_id', $tenant->id)->findOrFail($id);

        // Reconcile with the gateway while the payment is still pending.
        if ($subscription->status === 'pending' && $subscription->reference_number) {
            $gateway = $this->sonic->orderStatus($subscription->reference_number);
            $status = $this->sonic->extractStatus($gateway);

            if (in_array($status, ['COMPLETED', 'SUCCESS'], true)) {
                $subscription->activate();
            } elseif (in_array($status, ['FAILED', 'CANCELLED', 'USERCANCELLED', 'REJECTED', 'EXPIRED'], true)) {
                $subscription->markFailed();
            }
        }

        return response()->json(['status' => $subscription->fresh()->status]);
    }
}
