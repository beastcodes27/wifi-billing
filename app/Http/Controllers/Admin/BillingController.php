<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        $plans = \App\Models\SaasPlan::where('is_active', true)->get();
        return view('admin.billing', compact('plans'));
    }

    public function pay(Request $request)
    {
        $tenant = app(\App\Services\TenantContext::class)->getTenant();
        $subscription = $tenant->currentSubscription;
        $plan = $subscription ? $subscription->plan : null;

        $validated = $request->validate([
            'phone' => 'required|string',
            'plan_id' => 'required|exists:saas_plans,id',
        ]);

        $plan = \App\Models\SaasPlan::find($validated['plan_id']);

        if (!$plan) {
            return back()->with('error', 'Kifurushi ulichochagua hakipo.');
        }

        $transactionRef = 'SUB-' . time() . '-' . mt_rand(100, 999);
        $amount = $plan->price;

        if ($amount <= 0) {
            // Auto activate free plan
            \App\Models\SaasSubscription::updateOrCreate(
                ['tenant_id' => $tenant->id],
                [
                    'saas_plan_id' => $plan->id,
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => now()->addMonths($plan->billing_cycle_months),
                ]
            );
            return redirect()->route('admin.dashboard')->with('success', 'Umefanikiwa kujiunga na kifurushi cha bure!');
        }

        $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($phone, '0')) {
            $phone = '255' . substr($phone, 1);
        } elseif (strlen($phone) == 9) {
            $phone = '255' . $phone;
        }

        $baseUrl = env('PALM_PESA_BASE_URL');
        $apiKey = env('PALM_PESA_API_KEY'); // MUST use system API for Super Admin
        $userId = env('PALM_PESA_USER_ID');
        $vendor = env('PALM_PESA_VENDOR');

        try {
            $response = \Illuminate\Support\Facades\Http::retry(3, 100)->withToken($apiKey)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post("$baseUrl/pay-via-mobile", [
                    'user_id' => (string) $userId,
                    'name' => 'SaaS Subscription',
                    'email' => 'admin@wifi-billing.co.tz',
                    'phone' => $phone,
                    'amount' => (int) $amount,
                    'transaction_id' => $transactionRef,
                    'address' => 'Tanzania',
                    'postcode' => '00000',
                    'buyer_uuid' => $tenant->id
                ]);

            $responseData = $response->json();
            
            $isSuccessful = false;
            if (isset($responseData['response']['resultcode']) && $responseData['response']['resultcode'] === '000') {
                $isSuccessful = true;
            } elseif (isset($responseData['response']['result']) && $responseData['response']['result'] === 'SUCCESS') {
                $isSuccessful = true;
            }

            $orderId = $responseData['response']['order_id'] ?? null;
            if (!$orderId && isset($responseData['order_id'])) {
                $orderId = $responseData['order_id'];
            }

            if ($response->successful() && $isSuccessful && $orderId) {
                // Save pending SaaS subscription
                $subscription = \App\Models\SaasSubscription::create([
                    'tenant_id' => $tenant->id,
                    'saas_plan_id' => $plan->id,
                    'amount_paid' => $amount,
                    'payment_method' => 'palm_pesa',
                    'reference_number' => $orderId,
                    'status' => 'pending',
                ]);

                return redirect()->route('admin.billing.processing', $subscription->id);
            } else {
                \Illuminate\Support\Facades\Log::error('SaaS Palm Pesa Initiation Failed', ['response' => $responseData]);
                return back()->with('error', 'Kuna tatizo kuanzisha malipo. Hakikisha namba ya simu ipo sahihi na ina salio.');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SaaS Palm Pesa Exception: ' . $e->getMessage());
            return back()->with('error', 'Hitilafu ya mtandao. Tafadhali jaribu tena.');
        }
    }

    public function processing($id)
    {
        $tenant = app(\App\Services\TenantContext::class)->getTenant();
        $subscription = \App\Models\SaasSubscription::where('tenant_id', $tenant->id)->findOrFail($id);

        if ($subscription->status === 'active') {
            return redirect()->route('admin.dashboard')->with('success', 'Malipo yamefanikiwa! Kifurushi chako kipo hewani.');
        }

        return view('admin.billing_processing', compact('subscription'));
    }

    public function status($id)
    {
        $tenant = app(\App\Services\TenantContext::class)->getTenant();
        $subscription = \App\Models\SaasSubscription::where('tenant_id', $tenant->id)->findOrFail($id);

        return response()->json([
            'status' => $subscription->status
        ]);
    }
}
