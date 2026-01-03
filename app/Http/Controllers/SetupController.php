<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SetupController extends Controller
{
    public function index()
    {
        return view('setup.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|string|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
            'branch_name' => 'required|string|max:255',
            'branch_location' => 'required|string|max:255',
        ]);

        // Create Branch first
        $branch = Branch::create([
            'name' => $request->branch_name,
            'location' => $request->branch_location,
        ]);

        // Create Admin User
        $user = User::create([
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'password' => Hash::make($request->admin_password),
            'role' => 'admin',
            'branch_id' => $branch->id,
        ]);

        // Login the user
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
