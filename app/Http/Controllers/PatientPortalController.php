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

        $recentActivity = $patient->sales()->with('items.product')->latest()->take(5)->get();

        return view('portal.dashboard', compact('patient', 'visitCount', 'prescriptionCount', 'loyaltyPoints', 'recentActivity'));
    }

    /**
     * Transaction History
     */
    public function transactions()
    {
        $user = Auth::user();
        $sales = $user->patient->sales()
            ->with(['items.product', 'refund'])
            ->latest()
            ->paginate(15);

        return view('portal.transactions', compact('sales'));
    }

    /**
     * Loyalty Points History
     */
    public function loyalty()
    {
        $user = Auth::user();
        $patient = $user->patient;
        $settings = \App\Models\Setting::first();

        // Get sales with loyalty activity
        $salesHistory = \App\Models\Sale::where('patient_id', $patient->id)
            ->where(function ($q) {
                $q->where('points_earned', '>', 0)
                    ->orWhere('points_redeemed', '>', 0);
            })
            ->latest()
            ->get()
            ->map(function ($sale) {
                return [
                    'type' => 'sale',
                    'id' => $sale->id,
                    'date' => $sale->created_at,
                    'points_earned' => $sale->points_earned ?? 0,
                    'points_redeemed' => $sale->points_redeemed ?? 0,
                    'amount' => $sale->total_amount,
                    'is_reversal' => false,
                ];
            });

        // Get approved refunds that affected loyalty points
        $refundHistory = \App\Models\Refund::where('status', 'approved')
            ->whereHas('sale', function ($q) use ($patient) {
                $q->where('patient_id', $patient->id)
                    ->where(function ($sq) {
                        $sq->where('points_earned', '>', 0)
                            ->orWhere('points_redeemed', '>', 0);
                    });
            })
            ->with('sale')
            ->get()
            ->map(function ($refund) {
                return [
                    'type' => 'refund',
                    'id' => $refund->sale_id,
                    'refund_id' => $refund->id,
                    'date' => $refund->updated_at,
                    'points_earned' => -($refund->sale->points_earned ?? 0),
                    'points_redeemed' => $refund->sale->points_redeemed ?? 0,
                    'amount' => $refund->refund_amount,
                    'is_reversal' => true,
                ];
            });

        // Combine and sort
        $transactions = $salesHistory->concat($refundHistory)->sortByDesc('date')->values();

        // Paginate manually
        $page = request()->get('page', 1);
        $perPage = 20;
        $loyaltyHistory = new \Illuminate\Pagination\LengthAwarePaginator(
            $transactions->forPage($page, $perPage),
            $transactions->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return view('portal.loyalty', compact('patient', 'loyaltyHistory', 'settings'));
    }

    /**
     * Medical History (Prescriptions)
     */
    public function history()
    {
        $user = Auth::user();
        $prescriptions = $user->patient->prescriptions()->with('doctor')->latest()->paginate(10);

        return view('portal.history', compact('prescriptions'));
    }

    /**
     * Upload Prescription Form
     */
    public function upload()
    {
        return view('portal.upload');
    }

    /**
     * Store Uploaded Prescription
     */
    public function storeUpload(Request $request)
    {
        $request->validate([
            'prescription_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $patient = $user->patient;
        $file = $request->file('prescription_file');

        // Store the file
        $path = $file->store('prescription-uploads', 'public');

        // Create a patient document record with all required fields
        $document = $patient->documents()->create([
            'title' => 'Prescription Upload - ' . now()->format('M d, Y'),
            'type' => 'prescription_upload',
            'file_path' => $path,
            'filename' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'description' => $request->notes,
            'notes' => $request->notes,
            'uploaded_by' => $user->id,
        ]);

        return redirect()->route('portal.upload')->with('success', 'Prescription uploaded successfully! Our team will review it and contact you shortly.');
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

