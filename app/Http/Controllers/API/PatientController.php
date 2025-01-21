<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    /**
     * Get all patients with filters
     */
    public function index(Request $request)
    {
        $query = Patient::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by location
        if ($request->has('localisation_id')) {
            $query->where('localisation_id', $request->input('localisation_id'));
        }

        $patients = $query->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $patients
        ]);
    }

    /**
     * Create new patient
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'required|date',
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'groupe_sanguin' => 'nullable|string|max:3',
            'allergies' => 'nullable|string',
            'maladies_chroniques' => 'nullable|string',
            'localisation_id' => 'required|exists:localisation,localisation_id'
        ]);

        DB::beginTransaction();
        try {
            // Create patient
            $patient = Patient::create($validatedData);

            // Create emergency card for patient
            $patient->carteUrgence()->create([
                'groupe_sanguin' => $request->groupe_sanguin,
                'allergies' => $request->allergies,
                'maladies_chroniques' => $request->maladies_chroniques
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Patient successfully created',
                'data' => $patient
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create patient',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update patient information
     */
    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $validatedData = $request->validate([
            'nom' => 'string|max:100',
            'prenom' => 'string|max:100',
            'date_naissance' => 'date',
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'groupe_sanguin' => 'nullable|string|max:3',
            'allergies' => 'nullable|string',
            'maladies_chroniques' => 'nullable|string',
            'localisation_id' => 'exists:localisation,localisation_id'
        ]);

        DB::beginTransaction();
        try {
            $patient->update($validatedData);

            // Update emergency card if exists
            if ($patient->carteUrgence) {
                $patient->carteUrgence->update([
                    'groupe_sanguin' => $request->groupe_sanguin,
                    'allergies' => $request->allergies,
                    'maladies_chroniques' => $request->maladies_chroniques
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Patient successfully updated',
                'data' => $patient
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update patient',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search patients
     */
    public function search(Request $request)
    {
        $query = Patient::query();

        // Apply search filters
        if ($request->has('nom')) {
            $query->where('nom', 'like', '%' . $request->nom . '%');
        }
        if ($request->has('prenom')) {
            $query->where('prenom', 'like', '%' . $request->prenom . '%');
        }
        if ($request->has('date_naissance')) {
            $query->where('date_naissance', $request->date_naissance);
        }

        $patients = $query->with(['carteUrgence', 'localisation'])->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $patients
        ]);
    }

    public function exporterPatients(Request $request)
    {
        return Excel::download(
            new PatientsExport($request->all()),
            'patients.xlsx'
        );
    }
}


