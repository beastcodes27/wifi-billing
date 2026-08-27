<?php

namespace App\Services;

use RouterOS\Client;
use RouterOS\Query;
use App\Models\Router;
use Illuminate\Support\Facades\Log;

class MikrotikService
{
    protected $client;
    protected $connected = false;

    /**
     * Pass an optional App\Models\Router object to connect to a specific router.
     * If no router is passed, it attempts to load a default from env or the first active one.
     */
    public function __construct(Router $router = null)
    {
        try {
            if (!$router) {
                // Fallback to first active router in DB, or env variables
                $router = Router::where('is_active', true)->whereNotNull('api_host')->first();
            }

            if ($router && $router->api_host && $router->api_user) {
                $host = $router->api_host;
                $user = $router->api_user;
                $pass = $router->api_password ?? '';
                $port = $router->api_port ?? 8728;
            } else {
                $host = env('MIKROTIK_HOST', '192.168.88.1');
                $user = env('MIKROTIK_USER', 'admin');
                $pass = env('MIKROTIK_PASS', '');
                $port = (int) env('MIKROTIK_PORT', 8728);
            }

            $this->client = new Client([
                'host' => $host,
                'user' => $user,
                'pass' => $pass,
                'port' => $port,
            ]);
            
            $this->connected = true;
            
        } catch (\Exception $e) {
            Log::error('Mikrotik Connection Error: ' . $e->getMessage());
            $this->connected = false;
        }
    }

    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * Add a user to Mikrotik Hotspot
     */
    public function addHotspotUser($username, $password, $profile, $limitUptime, $macAddress = null)
    {
        if (!$this->connected) return false;

        $query = (new Query('/ip/hotspot/user/add'))
            ->equal('name', $username)
            ->equal('password', $password)
            ->equal('limit-uptime', $limitUptime);

        if ($profile) {
            $query->equal('profile', $profile);
        }

        if ($macAddress) {
            $query->equal('mac-address', $macAddress);
        }

        try {
            $response = $this->client->query($query)->read();
            return $response;
        } catch (\Exception $e) {
            Log::error('Mikrotik Add User Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove a user from Hotspot
     */
    public function removeHotspotUser($username)
    {
        if (!$this->connected) return false;

        try {
            // Find user ID first
            $findQuery = (new Query('/ip/hotspot/user/print'))
                ->where('name', $username);
            $user = $this->client->query($findQuery)->read();

            if (!empty($user) && isset($user[0]['.id'])) {
                $removeQuery = (new Query('/ip/hotspot/user/remove'))
                    ->equal('.id', $user[0]['.id']);
                return $this->client->query($removeQuery)->read();
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Mikrotik Remove User Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Test connection manually
     */
    public function testConnection()
    {
        if (!$this->connected) return false;
        
        try {
            $query = new Query('/system/resource/print');
            $response = $this->client->query($query)->read();
            return !empty($response);
        } catch (\Exception $e) {
            Log::error('Mikrotik Test Connection Error: ' . $e->getMessage());
            return false;
        }
    }
}
