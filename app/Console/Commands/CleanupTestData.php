<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WifiUser;
use App\Models\Transaction;

class CleanupTestData extends Command
{
    protected $signature = 'system:cleanup-tests';
    protected $description = 'Safisha vocha zote za majaribio ambazo hazijatumika bila kugusa zinazotumika';

    public function handle()
    {
        $this->info('Inaanza kufanya usafi wa database...');

        // 1. Futa vocha zote ambazo hazijatumika (activated_at is null)
        $unusedUsers = WifiUser::whereNull('activated_at')->get();
        $count = $unusedUsers->count();
        
        // 2. Futa transactions za vocha hizo
        $userIds = $unusedUsers->pluck('id')->toArray();
        if (!empty($userIds)) {
            Transaction::whereIn('wifi_user_id', $userIds)->delete();
            WifiUser::whereIn('id', $userIds)->delete();
        }

        // 3. Futa pending/failed transactions zote
        $pendingTxns = Transaction::where('status', 'pending')
            ->orWhere('status', 'failed')
            ->delete();

        $this->info("Usafi umekamilika! Imefuta vocha {$count} ambazo hazijatumika na miamala yake.");
    }
}
