<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientDocumentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Patient $patient)
    {
        $request->validate([
            'document' => 'required|file|max:10240|mimes:jpeg,png,jpg,pdf,doc,docx', // Max 10MB
            'label' => 'nullable|string|max:100', // e.g., "Blood Test", "X-Ray"
            'description' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('document')) {
            $file = $request->file('document');

            // Generate a safe unique filename to avoid conflicts
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $safeName = pathinfo($originalName, PATHINFO_FILENAME) . '_' . time() . '.' . $extension;

            // Store file in 'patient_documents' directory on the 'public' disk
            $path = $file->storeAs('patient_documents', $safeName, 'public');

            $patient->documents()->create([
                'file_path' => $path,
                'filename' => $originalName,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'label' => $request->label,
                'description' => $request->description,
            ]);

            return back()->with('success', 'Document uploaded successfully.');
        }

        return back()->withErrors(['document' => 'File upload failed.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PatientDocument $document)
    {
        // Delete file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        // Delete database record
        $document->delete();

        return back()->with('success', 'Document deleted successfully.');
    }
}
