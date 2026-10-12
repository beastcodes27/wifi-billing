<?php

namespace App\Console\Commands;

use App\Models\SaasSubscription;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    protected $signature = 'saas:expire-subscriptions';

    protected $description = 'Mark expired SaaS subscriptions (active or trial) as expired';

    public function handle(): int
    {
        $count = SaasSubscription::whereIn('status', ['active', 'trial'])
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->update(['status' => 'expired']);

        $this->info("Expired {$count} SaaS subscription(s).");

        return self::SUCCESS;
    }
}
