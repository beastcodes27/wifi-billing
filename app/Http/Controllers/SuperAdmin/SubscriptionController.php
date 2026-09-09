<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = \App\Models\SaasSubscription::with(['tenant', 'plan'])->latest()->get();
        return view('superadmin.subscriptions', compact('subscriptions'));
    }
}
