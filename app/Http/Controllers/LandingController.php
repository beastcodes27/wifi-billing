<?php

namespace App\Http\Controllers;

use App\Models\SaasPlan;

class LandingController extends Controller
{
    /**
     * Public marketing homepage.
     */
    public function index()
    {
        $plans = SaasPlan::publiclySelectable()->orderBy('price')->get();

        return view('landing', compact('plans'));
    }
}
