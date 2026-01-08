<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientPortalController extends Controller
{
    /**
     * Patient Dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        if (!$user->patient) {
            return redirect()->route('dashboard')->with('error', 'No patient record linked to this account.');
        }

        $patient = $user->patient;

        // Stats
        $visitCount = $patient->sales()->count();
        $prescriptionCount = $patient->prescriptions()->count();
        $loyaltyPoints = $patient->loyalty_points;

        $recentActivity = $patient->sales()->latest()->take(5)->get();

        return view('portal.dashboard', compact('patient', 'visitCount', 'prescriptionCount', 'loyaltyPoints', 'recentActivity'));
    }

    /**
     * Medical History (Prescriptions)
     */
    public function history()
    {
        $user = Auth::user();
        $prescriptions = $user->patient->prescriptions()->latest()->paginate(10);

        return view('portal.history', compact('prescriptions'));
    }

    /**
     * Documents
     */
    public function documents()
    {
        $user = Auth::user();
        $documents = $user->patient->documents()->latest()->get();

        return view('portal.documents', compact('documents'));
    }

    /**
     * Profile Settings
     */
    public function profile()
    {
        return view('portal.profile', ['user' => Auth::user(), 'patient' => Auth::user()->patient]);
    }
}
