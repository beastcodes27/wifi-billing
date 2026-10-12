<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantContext;
use Illuminate\Http\Request;

class BrandingController extends Controller
{
    public function index()
    {
        $tenant = app(TenantContext::class)->getTenant();

        return view('admin.branding', [
            'tenant' => $tenant,
            'branding' => $tenant->branding(),
        ]);
    }

    public function update(Request $request)
    {
        $tenant = app(TenantContext::class)->getTenant();

        $validated = $request->validate([
            'display_name' => ['nullable', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:120'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:120'],
            'primary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
        ]);

        $settings = is_array($tenant->settings) ? $tenant->settings : [];
        $b = is_array($settings['branding'] ?? null) ? $settings['branding'] : [];

        foreach (['display_name', 'tagline', 'contact_phone', 'whatsapp', 'email', 'primary_color', 'accent_color'] as $field) {
            if (! empty($validated[$field])) {
                $b[$field] = $validated[$field];
            }
        }

        if ($request->boolean('remove_logo')) {
            unset($b['logo']);
            $tenant->logo = null;
        }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $dir = public_path('uploads/branding');
            if (! is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            $name = 'branding-' . $tenant->id . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $name);
            $b['logo'] = 'uploads/branding/' . $name;
            $tenant->logo = $b['logo'];
        }

        // Keep the denormalised columns in sync with primary branding fields.
        if (! empty($validated['display_name'])) {
            $tenant->business_name = $validated['display_name'];
        }
        if (! empty($validated['contact_phone'])) {
            $tenant->phone = $validated['contact_phone'];
        }
        if (! empty($validated['email'])) {
            $tenant->email = $validated['email'];
        }

        $settings['branding'] = $b;
        $tenant->settings = $settings;
        $tenant->save();

        return back()->with('success', 'Muonekano wa portal umehifadhiwa kikamilifu.');
    }
}
