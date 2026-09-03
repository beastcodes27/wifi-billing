<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Fetch all wifi users with their packages, sorted by latest activity
        $users = \App\Models\WifiUser::with('package')
            ->whereNotNull('activated_at')
            ->orderBy('is_online', 'desc')
            ->orderBy('last_logout_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.users', compact('users'));
    }
}
