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
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@mamarichibando.com'],
            [
                'name' => 'Admin Mama Richi Bando',
                'password' => bcrypt(env('ADMIN_PASSWORD')),
                'tenant_id' => 1,
            ]
        );

        // Default SaaS plans available for business self-registration.
        $this->call(SaasPlanSeeder::class);

        $packages = [
            ['name' => 'Masaa 4', 'price' => 500, 'duration_minutes' => 240, 'mikrotik_profile' => 'Masaa4'],
            ['name' => 'Masaa 24', 'price' => 1000, 'duration_minutes' => 1440, 'mikrotik_profile' => 'Masaa24'],
            ['name' => 'Wiki 1', 'price' => 4000, 'duration_minutes' => 10080, 'mikrotik_profile' => 'Wiki1'],
            ['name' => 'Mwezi 1', 'price' => 15000, 'duration_minutes' => 43200, 'mikrotik_profile' => 'Mwezi1'],
        ];

        foreach ($packages as $pkg) {
            \App\Models\Package::firstOrCreate(['name' => $pkg['name']], $pkg);
        }
    }
}
