<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Patient::withCount('prescriptions')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $patients = $query->paginate(10);
        return view('patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
        ]);

        $patient = Patient::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'patient' => $patient,
                'message' => 'Patient registered successfully.'
            ]);
        }

        return redirect()->route('patients.index')->with('success', 'Patient registered successfully.');
    }

    /**
     * Display the specified resource.
     * This acts as the Patient Portal View.
     */
    public function show(Patient $patient)
    {
        $patient->load(['prescriptions.items.product', 'sales.items.product', 'documents']);
        return view('patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
        ]);

        $patient->update($validated);

        return redirect()->route('patients.index')->with('success', 'Patient updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')->with('success', 'Patient deleted successfully.');
    }

    /**
     * AJAX Search for POS
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        $patients = Patient::where('name', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->limit(10)
            ->get();
        return response()->json($patients);
    }

    /**
     * AJAX Store for POS
     */
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:patients,phone|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $patient = Patient::create($validated);

        return response()->json([
            'success' => true,
            'patient' => $patient
        ]);
    }
    /**
     * Show Loyalty History
     */
    public function loyaltyHistory(Patient $patient)
    {
        // Get sales with loyalty activity
        $sales = \App\Models\Sale::where('patient_id', $patient->id)
            ->where(function ($q) {
                $q->where('points_earned', '>', 0)
                    ->orWhere('points_redeemed', '>', 0);
            })
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
        $refunds = \App\Models\Refund::where('status', 'approved')
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
                    'date' => $refund->updated_at, // When approved
                    'points_earned' => -($refund->sale->points_earned ?? 0), // Reversed (negative)
                    'points_redeemed' => $refund->sale->points_redeemed ?? 0, // Returned (positive for display)
                    'amount' => $refund->refund_amount,
                    'is_reversal' => true,
                ];
            });

        // Combine and sort by date descending
        $combined = $sales->concat($refunds)
            ->sortByDesc('date')
            ->values();

        // Manual pagination
        $page = request()->get('page', 1);
        $perPage = 20;
        $loyaltyTransactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $combined->forPage($page, $perPage),
            $combined->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return view('patients.loyalty', compact('patient', 'loyaltyTransactions'));
    }

    /**
     * Enable Patient Portal Access
     */
    public function enablePortal(Patient $patient)
    {
        if ($patient->user_id) {
            return back()->with('error', 'Portal access is already enabled for this patient.');
        }

        if (empty($patient->email)) {
            return back()->with('error', 'Patient requires an email address to enable portal access.');
        }

        // Check if email already used by another user
        if (\App\Models\User::where('email', $patient->email)->exists()) {
            return back()->with('error', 'This email address is already associated with another user account.');
        }

        $password = \Illuminate\Support\Str::random(10);

        $user = \App\Models\User::create([
            'name' => $patient->name,
            'email' => $patient->email,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role' => 'patient',
        ]);

        $patient->update(['user_id' => $user->id]);

        // Send email with login credentials
        $emailSent = false;
        $emailError = null;

        try {
            \Illuminate\Support\Facades\Mail::to($patient->email)
                ->send(new \App\Mail\PatientPortalAccess($patient, $password));
            $emailSent = true;

            // Log successful email to communication log
            \App\Models\CommunicationLog::create([
                'type' => 'email',
                'recipient' => $patient->email,
                'message' => 'Patient Portal Access Credentials',
                'status' => 'sent',
                'context' => 'patient_portal_access',
                'user_id' => auth()->id(),
                'branch_id' => auth()->user()?->branch_id,
            ]);
        } catch (\Exception $e) {
            $emailError = $e->getMessage();
            \Illuminate\Support\Facades\Log::error("Failed to send portal access email: " . $e->getMessage());

            // Log failed email to communication log
            \App\Models\CommunicationLog::create([
                'type' => 'email',
                'recipient' => $patient->email,
                'message' => 'Patient Portal Access Credentials',
                'status' => 'failed',
                'response' => $e->getMessage(),
                'context' => 'patient_portal_access',
                'user_id' => auth()->id(),
                'branch_id' => auth()->user()?->branch_id,
            ]);
        }

        if ($emailSent) {
            return back()->with('success', "Portal access enabled! Login credentials have been sent to {$patient->email}.");
        } else {
            // If email fails, still show password so user can manually provide it
            return back()->with('warning', "Portal access enabled but email failed to send. Temporary Password: {$password} (Please copy this and provide to patient manually)");
        }
    }
}
