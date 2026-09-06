<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Site;

class SiteController extends Controller
{
    public function index()
    {
        $sites = Site::withCount('routers')->get();
        return view('admin.sites.index', compact('sites'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string'
        ]);

        Site::create($request->all());

        return redirect()->route('admin.sites.index')->with('success', 'Site imeongezwa kikamilifu.');
    }

    public function destroy(Site $site)
    {
        $site->delete();
        return redirect()->route('admin.sites.index')->with('success', 'Site imefutwa kikamilifu.');
    }
}
