<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Scope tenant-bound lookups/creates to tenant #1 so seeding stays idempotent
        // (TenantScope otherwise fail-closes to tenant_id = 0 when no context is set).
        $tenant = \App\Models\Tenant::find(1);
        if ($tenant) {
            app(\App\Services\TenantContext::class)->setTenant($tenant);
        }

        $adminEmail = env('ADMIN_EMAIL', 'admin@mamarichibando.com');
        $adminName = env('ADMIN_NAME', 'System Owner');
        $providedPassword = env('ADMIN_PASSWORD');
        $generatedPassword = $providedPassword ?: \Illuminate\Support\Str::password(16);

        $admin = \App\Models\User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'password' => $generatedPassword,
                'tenant_id' => 1,
                'is_super_admin' => true,
            ]
        );

        if ($admin->wasRecentlyCreated && ! $providedPassword) {
            $this->command?->warn("Seeded super admin [{$adminEmail}] with a generated password: {$generatedPassword}");
            $this->command?->warn('Set ADMIN_PASSWORD in .env and change it right after first login.');
        }

        // Default SaaS plans available for business self-registration.
        $this->call(SaasPlanSeeder::class);

        $packages = [
            ['tenant_id' => 1, 'name' => 'Masaa 4', 'price' => 500, 'duration_minutes' => 240, 'mikrotik_profile' => 'Masaa4'],
            ['tenant_id' => 1, 'name' => 'Masaa 24', 'price' => 1000, 'duration_minutes' => 1440, 'mikrotik_profile' => 'Masaa24'],
            ['tenant_id' => 1, 'name' => 'Wiki 1', 'price' => 4000, 'duration_minutes' => 10080, 'mikrotik_profile' => 'Wiki1'],
            ['tenant_id' => 1, 'name' => 'Mwezi 1', 'price' => 15000, 'duration_minutes' => 43200, 'mikrotik_profile' => 'Mwezi1'],
        ];

        foreach ($packages as $pkg) {
            \App\Models\Package::firstOrCreate(['name' => $pkg['name'], 'tenant_id' => 1], $pkg);
        }
    }
}
