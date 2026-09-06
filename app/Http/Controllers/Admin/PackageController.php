<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $query = Package::query();

        // Sort
        $sort = $request->input('sort', 'display_order');
        $dir = $request->input('dir', 'asc');
        $allowed = ['name', 'price', 'duration_minutes', 'display_order', 'is_active'];
        if (in_array($sort, $allowed)) {
            $query->orderBy($sort, $dir === 'asc' ? 'asc' : 'desc');
        }

        $packages = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => Package::count(),
            'active' => Package::where('is_active', true)->count(),
            'inactive' => Package::where('is_active', false)->count(),
        ];

        return view('admin.packages.index', compact('packages', 'stats', 'sort', 'dir'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_value' => 'required|integer|min:1',
            'duration_unit' => 'required|string|in:Minutes,Hours,Days,Weeks,Months',
            'mikrotik_profile' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'download_speed' => 'nullable|string|max:50',
            'upload_speed' => 'nullable|string|max:50',
            'data_quota_mb' => 'nullable|integer|min:1',
            'display_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $multiplier = 1;
        switch ($request->duration_unit) {
            case 'Hours': $multiplier = 60; break;
            case 'Days': $multiplier = 1440; break;
            case 'Weeks': $multiplier = 10080; break;
            case 'Months': $multiplier = 43200; break; // 30 days
        }

        $packageData = [
            'name' => $validated['name'],
            'price' => $validated['price'],
            'duration_minutes' => $validated['duration_value'] * $multiplier,
            'mikrotik_profile' => $validated['mikrotik_profile'] ?? 'default',
            'description' => $validated['description'],
            'download_speed' => $validated['download_speed'] ?? null,
            'upload_speed' => $validated['upload_speed'] ?? null,
            'data_quota_mb' => $validated['data_quota_mb'] ?? null,
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $request->has('is_active') ? true : false,
        ];

        Package::create($packageData);

        return redirect()->route('admin.packages.index')->with('success', 'Kifurushi kimeongezwa kikamilifu!');
    }

    public function edit(Package $package)
    {
        // Calculate duration value and unit from minutes
        $minutes = $package->duration_minutes;
        $duration_value = $minutes;
        $duration_unit = 'Minutes';
        
        if ($minutes >= 43200 && $minutes % 43200 === 0) {
            $duration_value = $minutes / 43200;
            $duration_unit = 'Months';
        } elseif ($minutes >= 10080 && $minutes % 10080 === 0) {
            $duration_value = $minutes / 10080;
            $duration_unit = 'Weeks';
        } elseif ($minutes >= 1440 && $minutes % 1440 === 0) {
            $duration_value = $minutes / 1440;
            $duration_unit = 'Days';
        } elseif ($minutes >= 60 && $minutes % 60 === 0) {
            $duration_value = $minutes / 60;
            $duration_unit = 'Hours';
        }

        return view('admin.packages.edit', compact('package', 'duration_value', 'duration_unit'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_value' => 'required|integer|min:1',
            'duration_unit' => 'required|string|in:Minutes,Hours,Days,Weeks,Months',
            'mikrotik_profile' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'download_speed' => 'nullable|string|max:50',
            'upload_speed' => 'nullable|string|max:50',
            'data_quota_mb' => 'nullable|integer|min:1',
            'display_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $multiplier = 1;
        switch ($request->duration_unit) {
            case 'Hours': $multiplier = 60; break;
            case 'Days': $multiplier = 1440; break;
            case 'Weeks': $multiplier = 10080; break;
            case 'Months': $multiplier = 43200; break; // 30 days
        }

        $package->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'duration_minutes' => $validated['duration_value'] * $multiplier,
            'mikrotik_profile' => $validated['mikrotik_profile'] ?? 'default',
            'description' => $validated['description'],
            'download_speed' => $validated['download_speed'] ?? null,
            'upload_speed' => $validated['upload_speed'] ?? null,
            'data_quota_mb' => $validated['data_quota_mb'] ?? null,
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.packages.index')->with('success', 'Kifurushi kimesasishwa kikamilifu!');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return redirect()->route('admin.packages.index')->with('success', 'Kifurushi kimefutwa!');
    }

    public function toggleStatus(Package $package)
    {
        $package->update([
            'is_active' => !$package->is_active
        ]);

        $status = $package->is_active ? 'kimewashwa' : 'kimezimwa';
        return back()->with('success', "Kifurushi {$package->name} {$status}.");
    }
}
