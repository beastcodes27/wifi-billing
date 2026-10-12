<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\SaasPlan;
use App\Models\SaasSubscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /**
     * Show the public business self-registration form.
     */
    public function show()
    {
        $plans = SaasPlan::publiclySelectable()->orderBy('price')->get();

        return view('owner.register', compact('plans'));
    }

    /**
     * Provision a new tenant, its owner account and a free trial subscription.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'saas_plan_id' => ['required', 'integer'],
        ]);

        $plan = SaasPlan::publiclySelectable()->find($validated['saas_plan_id']);

        if (! $plan) {
            return back()->withInput()->withErrors([
                'saas_plan_id' => 'Kifurushi ulichochagua hakipatikani.',
            ]);
        }

        $user = DB::transaction(function () use ($validated, $plan) {
            $tenant = Tenant::create([
                'business_name' => $validated['business_name'],
                'owner_name' => $validated['owner_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'status' => 'trial',
            ]);

            $user = User::create([
                'name' => $validated['owner_name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'phone_number' => $validated['phone'],
                'tenant_id' => $tenant->id,
                'is_super_admin' => false,
            ]);

            // One month free trial by default.
            $trialDays = $plan->trial_days > 0 ? $plan->trial_days : 30;

            SaasSubscription::create([
                'tenant_id' => $tenant->id,
                'saas_plan_id' => $plan->id,
                'amount_paid' => 0,
                'payment_method' => 'trial',
                'reference_number' => 'TRIAL-' . strtoupper(Str::random(8)),
                'starts_at' => now(),
                'ends_at' => now()->addDays($trialDays),
                'status' => 'trial',
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')->with(
            'success',
            'Karibu! Akaunti yako ya biashara imefunguliwa. Unayo kipindi cha majaribio cha bure.'
        );
    }
}
