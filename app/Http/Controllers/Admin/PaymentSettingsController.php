<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentSettingsController extends Controller
{
    public function index()
    {
        $tenant = app(\App\Services\TenantContext::class)->getTenant();
        $settings = $tenant->settings ?? [];
        return view('admin.payment_settings', compact('tenant', 'settings'));
    }

    public function update(Request $request)
    {
        $tenant = app(\App\Services\TenantContext::class)->getTenant();
        
        $validated = $request->validate([
            'fimipay_public_id' => 'nullable|string',
            'fimipay_secret_key' => 'nullable|string',
            'fimipay_environment' => 'nullable|string|in:live,test',
            'fimipay_base_url' => 'nullable|string',
            // Optional legacy palmpesa
            'api_key' => 'nullable|string',
            'user_id' => 'nullable|string',
            'vendor' => 'nullable|string',
        ]);

        $settings = $tenant->settings ?? [];
        
        // Save FimiPay
        $settings['fimipay'] = [
            'public_id' => $validated['fimipay_public_id'] ?? ($settings['fimipay']['public_id'] ?? env('FIMIPAY_PUBLIC_ID')),
            'secret_key' => $validated['fimipay_secret_key'] ?? ($settings['fimipay']['secret_key'] ?? env('FIMIPAY_SECRET_KEY')),
            'environment' => $validated['fimipay_environment'] ?? ($settings['fimipay']['environment'] ?? env('FIMIPAY_ENVIRONMENT', 'live')),
            'base_url' => $validated['fimipay_base_url'] ?? ($settings['fimipay']['base_url'] ?? env('FIMIPAY_BASE_URL', 'https://fimipay.com/api/v1')),
        ];

        // Legacy PalmPesa
        if (!empty($validated['api_key']) || !empty($validated['user_id'])) {
            $settings['palmpesa'] = [
                'api_key' => $validated['api_key'] ?? '',
                'user_id' => $validated['user_id'] ?? '',
                'vendor' => $validated['vendor'] ?? '',
            ];
        }
        
        $tenant->settings = $settings;
        $tenant->save();

        return back()->with('success', 'Mipangilio ya FimiPay imehifadhiwa kikamilifu! Malipo ya mtandaoni yatapitia akaunti yako ya FimiPay.');
    }
}
