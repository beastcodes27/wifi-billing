<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

class ResetData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-data {--keep-vouchers : Usifute vocha zilizopo kwenye stoo, futa tu miamala ya fedha}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Weka upya mapato ya mfumo yasome 00 Tsh (Futa miamala, matumizi na taarifa za zamani)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $keepVouchers = $this->option('keep-vouchers');
        $this->info('Inaanza kufanya usafi na kuweka upya mapato kuwa Tsh 00...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $financialTables = [
            'transactions',
            'expenses',
            'app_notifications',
            'customer_ledgers',
        ];

        foreach ($financialTables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->line(" - Jedwali la `{$table}` limewekwa upya.");
            }
        }

        if (!$keepVouchers) {
            $voucherTables = [
                'wifi_users',
                'voucher_batches',
                'vouchers',
            ];
            foreach ($voucherTables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->line(" - Jedwali la `{$table}` limewekwa upya.");
                }
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Clear cache
        Cache::flush();

        $this->info('--------------------------------------------------');
        $this->info('USAFI UMEKAMILIKA! Dashibodi sasa inasoma Tsh 00.');
        $this->info('Routers, Vifurushi na Akaunti za Mfumo hazikuguswa.');
        $this->info('--------------------------------------------------');
    }
}
