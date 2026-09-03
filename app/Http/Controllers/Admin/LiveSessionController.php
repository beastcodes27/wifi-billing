<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WifiUser;

class LiveSessionController extends Controller
{
    public function index()
    {
        return view('admin.live_sessions');
    }

    public function data()
    {
        try {
            $users = WifiUser::with('package')
                ->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->whereNotNull('expires_at')
                            ->where('expires_at', '>', now());
                    })
                    ->orWhere('is_online', true);
                })
                ->orderBy('is_online', 'desc')
                ->orderBy('expires_at', 'desc')
                ->get()
                ->map(function ($user) {
                    $bytes = $user->data_used ?? 0;
                    $mb = $bytes / (1024 * 1024);
                    $formattedData = '';
                    
                    if ($mb >= 1024) {
                        $formattedData = number_format($mb / 1024, 2) . ' GB';
                    } elseif ($mb > 0) {
                        $formattedData = number_format($mb, 2) . ' MB';
                    } else {
                        $formattedData = '0 MB';
                    }

                    $phone = $user->phone_number;
                    if ($phone == 'Mteja wa Cash' || $phone == 'Batch Print' || empty($phone)) {
                        $phone = 'Vocha: ' . $user->mikrotik_username;
                    }

                    $mac = $user->mac_address;
                    if (!$mac || strpos($mac, '-address') !== false) {
                        $mac = 'Unknown Device';
                    }

                    $expiresAt = $user->expires_at ?: ($user->subscription ? $user->subscription->expires_at : null);
                    $timeLeftSeconds = 0;
                    $timeLeftHuman = 'Haijawekwa';
                    $timeLeftClass = 'ok';

                    if ($expiresAt) {
                        $timeLeftSeconds = max(0, (int) round(now()->diffInSeconds($expiresAt, false)));
                        if ($timeLeftSeconds <= 0) {
                            $timeLeftHuman = 'Imeisha!';
                            $timeLeftClass = 'danger';
                        } elseif ($timeLeftSeconds < 600) { // chini ya dakika 10
                            $mins = (int) ceil($timeLeftSeconds / 60);
                            $timeLeftHuman = "Dakika {$mins} tu!";
                            $timeLeftClass = 'danger';
                        } elseif ($timeLeftSeconds < 3600) { // chini ya saa 1
                            $mins = (int) floor($timeLeftSeconds / 60);
                            $timeLeftHuman = "Dakika {$mins}";
                            $timeLeftClass = 'warning';
                        } elseif ($timeLeftSeconds < 86400) { // chini ya siku 1
                            $hrs  = (int) floor($timeLeftSeconds / 3600);
                            $mins = (int) floor(($timeLeftSeconds % 3600) / 60);
                            $timeLeftHuman = "Saa {$hrs}:{$mins}m";
                            $timeLeftClass = 'ok';
                        } else {
                            $days = (int) floor($timeLeftSeconds / 86400);
                            $timeLeftHuman = "Siku {$days}";
                            $timeLeftClass = 'ok';
                        }
                    } else {
                        // Fallback: calculate from package duration if available
                        if ($user->package && $user->package->duration_minutes && $user->activated_at) {
                            $calcExp = $user->activated_at->copy()->addMinutes($user->package->duration_minutes);
                            $timeLeftSeconds = max(0, (int) round(now()->diffInSeconds($calcExp, false)));
                            $days = (int) floor($timeLeftSeconds / 86400);
                            $timeLeftHuman = $days > 0 ? "Siku {$days}" : "Saa " . floor($timeLeftSeconds / 3600);
                            $timeLeftClass = $timeLeftSeconds <= 0 ? 'danger' : 'ok';
                        } else {
                            $timeLeftClass = 'warning';
                            $timeLeftHuman = 'Haijawekwa';
                        }
                    }

                    $isLive = (bool) $user->is_online;

                    return [
                        'id'                   => $user->id,
                        'phone'                => $phone,
                        'voucher'              => $user->mikrotik_username,
                        'package'              => $user->package ? $user->package->name : 'Bando Hai',
                        'is_live'              => $isLive,
                        'status_label'         => $isLive ? 'Live Hewani' : 'Hayupo Live',
                        'data_used'            => $formattedData,
                        'uptime'               => $isLive ? ($user->session_time ?: 'Muda mfupi') : 'Haijaunganishwa',
                        'activated_at'         => $user->activated_at ? $user->activated_at->format('d M H:i') : '-',
                        'activated_human'      => $user->activated_at ? $user->activated_at->diffForHumans() : '-',
                        'device'               => $mac,
                        'ip'                   => $user->ip_address ?? 'N/A',
                        'expires_at'           => $expiresAt ? $expiresAt->format('d M H:i') : null,
                        'time_left_seconds'    => $timeLeftSeconds,
                        'time_left_human'      => $timeLeftHuman,
                        'time_left_class'      => $timeLeftClass,
                    ];
                });

            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
