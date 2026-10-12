<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessPoint;
use App\Models\Site;
use App\Services\StickerScannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AccessPointController extends Controller
{
    protected array $brands = ['MikroTik', 'Ubiquiti UniFi', 'TP-Link Omada'];

    public function index()
    {
        $accessPoints = AccessPoint::with('site')->latest()->get();

        return view('admin.access_points.index', compact('accessPoints'));
    }

    public function create()
    {
        $sites = Site::orderBy('name')->get();
        $brands = $this->brands;

        return view('admin.access_points.create', compact('sites', 'brands'));
    }

    /**
     * AJAX: read a sticker photo and return the extracted fields.
     */
    public function scan(Request $request, StickerScannerService $scanner)
    {
        $request->validate([
            'photo' => 'required|image|max:8192',
        ]);

        try {
            $result = $scanner->scan($request->file('photo')->getRealPath());

            return response()->json(['ok' => true] + $result);
        } catch (\Throwable $e) {
            Log::warning('Access point sticker scan failed: ' . $e->getMessage());

            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        AccessPoint::create([
            'site_id' => $validated['site_id'] ?? null,
            'name' => $validated['name'] ?? ($validated['model'] ?? $validated['brand']),
            'brand' => $validated['brand'],
            'model' => $validated['model'] ?? null,
            'serial_number' => $validated['serial_number'] ?? null,
            'device_key' => $validated['device_key'] ?? null,
            'mac_address' => $validated['mac_address'] ?? null,
            'management_ip' => $validated['management_ip'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'adoption_status' => 'pending',
            'adoption_method' => $validated['adoption_method'] ?? 'manual',
            'scan_confidence' => $validated['scan_confidence'] ?? null,
            'scan_raw' => $this->decodeScanRaw($request->input('scan_raw')),
        ]);

        return redirect()->route('admin.access_points.index')
            ->with('success', 'Access Point imesajiliwa. Unaweza kuiadopti sasa.');
    }

    public function edit(AccessPoint $accessPoint)
    {
        $sites = Site::orderBy('name')->get();
        $brands = $this->brands;

        return view('admin.access_points.edit', compact('accessPoint', 'sites', 'brands'));
    }

    public function update(Request $request, AccessPoint $accessPoint)
    {
        $validated = $this->validateData($request);

        $accessPoint->update([
            'site_id' => $validated['site_id'] ?? null,
            'name' => $validated['name'] ?? ($validated['model'] ?? $validated['brand']),
            'brand' => $validated['brand'],
            'model' => $validated['model'] ?? null,
            'serial_number' => $validated['serial_number'] ?? null,
            'device_key' => $validated['device_key'] ?? null,
            'mac_address' => $validated['mac_address'] ?? null,
            'management_ip' => $validated['management_ip'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.access_points.index')->with('success', 'Access Point imesasishwa.');
    }

    public function adopt(AccessPoint $accessPoint)
    {
        $accessPoint->update([
            'adoption_status' => 'adopted',
            'adopted_at' => now(),
            'last_seen_at' => now(),
        ]);

        return back()->with('success', 'Access Point imeadoptiwa kikamilifu.');
    }

    public function destroy(AccessPoint $accessPoint)
    {
        $accessPoint->delete();

        return redirect()->route('admin.access_points.index')->with('success', 'Access Point imefutwa.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'site_id' => ['nullable', Rule::exists('sites', 'id')],
            'name' => ['nullable', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'device_key' => ['nullable', 'string', 'max:255'],
            'mac_address' => ['nullable', 'string', 'max:64'],
            'management_ip' => ['nullable', 'string', 'max:64'],
            'notes' => ['nullable', 'string'],
            'adoption_method' => ['nullable', 'string', 'max:20'],
            'scan_confidence' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);
    }

    protected function decodeScanRaw(?string $raw): ?array
    {
        if (empty($raw)) {
            return null;
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : null;
    }
}
