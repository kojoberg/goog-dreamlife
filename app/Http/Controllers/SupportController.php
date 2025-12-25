<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupportController extends Controller
{
    /**
     * Display the SOP Manual and Support page.
     */
    public function index()
    {
        return view('support.index');
    }
}
