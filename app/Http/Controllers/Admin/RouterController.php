<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Router;
use App\Models\Site;

class RouterController extends Controller
{
    public function index()
    {
        $routers = Router::with('site')->get();
        return view('admin.routers.index', compact('routers'));
    }

    public function create()
    {
        $sites = Site::all();
        return view('admin.routers.create', compact('sites'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'site_id' => 'required|exists:sites,id',
            'name' => 'required|string|max:255',
            'router_type' => 'required|string',
            'management_ip' => 'nullable|string',
            'api_host' => 'nullable|string',
            'api_user' => 'nullable|string',
            'api_password' => 'nullable|string',
            'api_port' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $data = $request->only(['site_id', 'name', 'router_type', 'management_ip', 'api_host', 'api_port', 'notes']);
        $data['status'] = $request->has('is_active') ? 'active' : 'inactive';
        $data['api_username'] = $request->api_user;
        // In a real app we might encrypt this, but to match the previous logic where it was just stored
        $data['api_password_encrypted'] = $request->api_password;

        $router = Router::create($data);

        return redirect()->route('admin.routers.index')->with('success', 'Router imesajiliwa kikamilifu.');
    }

    public function edit(Router $router)
    {
        $sites = Site::all();
        return view('admin.routers.edit', compact('router', 'sites'));
    }

    public function update(Request $request, Router $router)
    {
        $request->validate([
            'site_id' => 'required|exists:sites,id',
            'name' => 'required|string|max:255',
            'router_type' => 'required|string',
            'management_ip' => 'nullable|string',
            'api_host' => 'nullable|string',
            'api_user' => 'nullable|string',
            'api_password' => 'nullable|string',
            'api_port' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $data = $request->only(['site_id', 'name', 'router_type', 'management_ip', 'api_host', 'api_port', 'notes']);
        $data['status'] = $request->has('is_active') ? 'active' : 'inactive';
        if ($request->has('api_user')) {
            $data['api_username'] = $request->api_user;
        }
        if ($request->filled('api_password')) {
            $data['api_password_encrypted'] = $request->api_password;
        }

        $router->update($data);

        return redirect()->route('admin.routers.index')->with('success', 'Router imesasishwa kikamilifu.');
    }

    public function provision(Router $router)
    {
        return view('admin.routers.provision', compact('router'));
    }

    public function downloadLoginHtml()
    {
        $appUrl = rtrim((string) config('app.url'), '/');
        if (empty($appUrl) || str_contains($appUrl, 'localhost')) {
            $appUrl = 'https://drinkpoint.store';
        }

        // Resolve this tenant's router so the guest portal is tenant-branded.
        $tenant = app(\App\Services\TenantContext::class)->getTenant();
        $tokenParam = '';
        if ($tenant) {
            $router = \App\Models\Router::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->orderBy('id')
                ->first();
            if ($router && $router->sync_token) {
                $tokenParam = '&token=' . $router->sync_token;
            }
        }

        $portalUrl = $appUrl . '/portal?mac=$(mac)&ip=$(ip)&link-login=$(link-login-only)&link-login-only=$(link-login-only)&link-orig=$(link-orig-esc)&error=$(error)' . $tokenParam;

        $html = '<!DOCTYPE html>
<html>
<head>
    <title>Inakuunganisha...</title>
    <!-- 
    HILI NI FAILI LA MIKROTIK: login.html
    MAAGIZO:
    Buruta faili hili na kuliweka kwenye Winbox -> Files -> hotspot
    -->
    <meta http-equiv="refresh" content="0; url=' . $portalUrl . '">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: sans-serif; text-align: center; margin-top: 50px; background: #0c0c0e; color: #fff; }
        .loader { border: 4px solid rgba(255,255,255,0.15); border-top: 4px solid #FF3300; border-radius: 50%; width: 44px; height: 44px; animation: spin 1s linear infinite; margin: 25px auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <h3 style="font-weight: 700; margin-bottom: 8px;">Inakuunganisha na Mfumo wa WiFi...</h3>
    <div class="loader"></div>
    <p style="color: #94a3b8; font-size: 14px;">Tafadhali subiri kidogo unaelekezwa kwenye mfumo.</p>
    <script>
        setTimeout(function() {
            window.location.href = "' . $portalUrl . '";
        }, 1200);
    </script>
</body>
</html>';

        return response($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'attachment; filename="login.html"',
        ]);
    }

    public function destroy(Router $router)
    {
        $router->delete();
        return redirect()->route('admin.routers.index')->with('success', 'Router imefutwa kikamilifu.');
    }
}
