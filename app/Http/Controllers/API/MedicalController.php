<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\ExamenLaboratoire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicalController extends Controller
{
    /**
     * Get medical records for a patient
     */
    public function getRecords($patientId)
    {
        $records = DB::table('carnet_numerique')
            ->where('patient_id', $patientId)
            ->orderBy('date_enregistrement', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $records
        ]);
    }

    /**
     * Get consultations for a patient
     */
    public function getConsultations($patientId)
    {
        $consultations = Consultation::where('patient_id', $patientId)
            ->with('medecin')
            ->orderBy('date_consultation', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $consultations
        ]);
    }

    /**
     * Create new consultation
     */
    public function storeConsultation(Request $request)
    {
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'date_consultation' => 'required|date',
            'type_consultation' => 'required|in:routine,urgence,suivi,specialiste',
            'centre_sante' => 'required|string',
            'symptomes' => 'nullable|string',
            'diagnostic' => 'nullable|string',
            'prescriptions' => 'nullable|string',
            'observations' => 'nullable|string'
        ]);

        $consultation = Consultation::create($validatedData + [
            'medecin_id' => auth()->id()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Consultation created successfully',
            'data' => $consultation
        ], 201);
    }

    /**
     * Get examinations for a patient
     */
    public function getExaminations($patientId)
    {
        $examens = ExamenLaboratoire::where('patient_id', $patientId)
            ->with(['medecin', 'documents'])
            ->orderBy('date_examen', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $examens
        ]);
    }

    /**
     * Create new examination
     */
    public function storeExamination(Request $request)
    {
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'type_examen' => 'required|in:LMB,radio,echo,autre',
            'date_examen' => 'required|date',
            'resultat' => 'nullable|string',
            'centre_examen' => 'required|string'
        ]);

        $examen = ExamenLaboratoire::create($validatedData + [
            'medecin_id' => auth()->id()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Examination created successfully',
            'data' => $examen
        ], 201);
    }
}
