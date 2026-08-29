<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WifiUser;
use App\Services\MikrotikService;
use RouterOS\Query;
use Illuminate\Support\Facades\Log;

class CleanExpiredUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-expired-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes users from Mikrotik whose continuous time has expired';

    /**
     * Execute the console command.
     */
    public function handle(MikrotikService $mikrotikService)
    {
        $this->info("Checking for expired users...");

        // Fetch users who have expired but are not yet marked as 'deleted' or similar locally.
        // We'll use a new column 'is_expired' or just rely on 'expires_at' and 'is_online'.
        // Since we don't have an 'is_expired' column, let's just fetch those where expires_at < now()
        // and they are still in the system. But we need a way to not check the same users repeatedly.
        // We can add a 'synced_expiration' boolean or just delete them from the WifiUser table? 
        // We probably shouldn't delete them for history. Let's add a column 'is_expired' in migration, 
        // or just check if they are still online / not expired yet.
        // Wait, since we can't run migrations right now (DB is offline), we can just check if expires_at is past
        // and 'is_online' is true, or maybe we can delete the record in Mikrotik and we just won't find them next time.
        // But making API calls to Mikrotik for already deleted users will be slow and spam the logs.
        // Let's use 'is_online' = false. Wait, they can be expired AND offline.
        // How about we just fetch the active users from Mikrotik, and if any of them are expired, we remove them?
        // Let's just do: expires_at < now() AND is_active = true. 
        // Wait, `WifiUser` doesn't have `is_active`. 
        // It has: phone_number, mac_address, ip_address, mikrotik_username, mikrotik_password, package_id, expires_at, activated_at, is_online, data_used, session_time, last_logout_at.
        // If we just remove them from Mikrotik, we can nullify the `mikrotik_password` or set `expires_at` to null? No, we need `expires_at` for history.
        // Let's just remove them from Mikrotik, and set `mikrotik_username` to `null` to indicate they are removed from the router.
        
        $expiredUsers = WifiUser::whereNotNull('mikrotik_username')
                                ->whereNotNull('expires_at')
                                ->where('expires_at', '<', now())
                                ->get();

        if ($expiredUsers->isEmpty()) {
            $this->info("No expired users found.");
            return;
        }

        if (!$mikrotikService->isConnected()) {
            $this->error("Not connected to Mikrotik.");
            return;
        }

        $client = (new \ReflectionClass($mikrotikService))->getProperty('client');
        $client->setAccessible(true);
        $client = $client->getValue($mikrotikService);

        foreach ($expiredUsers as $user) {
            $username = $user->mikrotik_username;
            $this->info("Removing expired user: {$username}");

            try {
                // Find user ID first
                $findQuery = (new Query('/ip/hotspot/user/print'))->where('name', $username);
                $mikrotikUser = $client->query($findQuery)->read();

                if (!empty($mikrotikUser) && isset($mikrotikUser[0]['.id'])) {
                    $removeQuery = (new Query('/ip/hotspot/user/remove'))->equal('.id', $mikrotikUser[0]['.id']);
                    $client->query($removeQuery)->read();
                }

                // Also remove active session if any
                $findActive = (new Query('/ip/hotspot/active/print'))->where('user', $username);
                $activeSession = $client->query($findActive)->read();
                if (!empty($activeSession) && isset($activeSession[0]['.id'])) {
                    $removeActive = (new Query('/ip/hotspot/active/remove'))->equal('.id', $activeSession[0]['.id']);
                    $client->query($removeActive)->read();
                }

                // Clear mikrotik_username so we don't process them again
                $user->update([
                    'mikrotik_username' => null,
                    'is_online' => false
                ]);
                Log::info("Successfully removed expired user {$username}");

            } catch (\Exception $e) {
                Log::error("Error removing user {$username}: " . $e->getMessage());
                $this->error("Failed to remove {$username}");
            }
        }

        $this->info("Cleanup complete.");
    }
}
