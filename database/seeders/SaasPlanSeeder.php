<?php

namespace Database\Seeders;

use App\Models\SaasPlan;
use Illuminate\Database\Seeder;

class SaasPlanSeeder extends Seeder
{
    /**
     * Default public SaaS plans offered during self-registration.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'description' => 'Kwa biashara ndogo zinazoanza.',
                'price' => 10000,
                'billing_cycle_months' => 1,
                'trial_days' => 30,
                'max_routers' => 1,
                'max_customers' => 100,
                'is_active' => true,
                'is_public' => true,
                'has_auto_payment' => true,
            ],
            [
                'name' => 'Business',
                'description' => 'Kwa biashara zinazokua.',
                'price' => 25000,
                'billing_cycle_months' => 1,
                'trial_days' => 30,
                'max_routers' => 3,
                'max_customers' => 500,
                'is_active' => true,
                'is_public' => true,
                'has_auto_payment' => true,
            ],
            [
                'name' => 'Pro',
                'description' => 'Kwa biashara kubwa zenye maeneo mengi.',
                'price' => 50000,
                'billing_cycle_months' => 1,
                'trial_days' => 30,
                'max_routers' => 10,
                'max_customers' => 2000,
                'is_active' => true,
                'is_public' => true,
                'has_auto_payment' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SaasPlan::firstOrCreate(['name' => $plan['name']], $plan);
        }
    }
}
