<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConsultationController extends Controller
{
    /**
     * Affiche une liste paginée des consultations.
     */
    public function index(Request $request)
    {
        $consultations = Consultation::with(['patient', 'medecin'])
            ->orderBy('date_consultation', 'desc')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data'   => $consultations,
        ]);
    }

    /**
     * Affiche le détail d'une consultation.
     */
    public function show($id)
    {
        $consultation = Consultation::with(['patient', 'medecin'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $consultation,
        ]);
    }

    /**
     * Crée une nouvelle consultation.
     */
    public function store(Request $request)
    {
        // Validation des données
        $validatedData = $request->validate([
            'patient_id'        => 'required|exists:patients,patient_id',
            'date_consultation' => 'required|date',
            'type_consultation' => 'required|in:routine,urgence,suivi,specialiste',
            'centre_sante'      => 'required|string',
            'symptomes'         => 'nullable|string',
            'diagnostic'        => 'nullable|string',
            'prescriptions'     => 'nullable|string',
            'observations'      => 'nullable|string',
            'hospitalisation'   => 'nullable|boolean',
            'duree_hospitalisation' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            // Si le medecin n'est pas passé dans la requête, on le définit comme l'utilisateur authentifié.
            if (!$request->has('medecin_id')) {
                $validatedData['medecin_id'] = auth()->id();
            }

            $consultation = Consultation::create($validatedData);

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Consultation créée avec succès',
                'data'    => $consultation,
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status'  => 'error',
                'message' => 'Erreur lors de la création de la consultation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Met à jour une consultation existante.
     */
    public function update(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);

        // Validation des données de mise à jour
        $validatedData = $request->validate([
            'patient_id'        => 'sometimes|exists:patients,patient_id',
            'date_consultation' => 'sometimes|date',
            'type_consultation' => 'sometimes|in:routine,urgence,suivi,specialiste',
            'centre_sante'      => 'sometimes|string',
            'symptomes'         => 'nullable|string',
            'diagnostic'        => 'nullable|string',
            'prescriptions'     => 'nullable|string',
            'observations'      => 'nullable|string',
            'hospitalisation'   => 'nullable|boolean',
            'duree_hospitalisation' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            $consultation->update($validatedData);
            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Consultation mise à jour avec succès',
                'data'    => $consultation,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status'  => 'error',
                'message' => 'Erreur lors de la mise à jour de la consultation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Supprime (ou annule) une consultation.
     */
    public function destroy($id)
    {
        $consultation = Consultation::findOrFail($id);

        DB::beginTransaction();
        try {
            $consultation->delete();
            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Consultation supprimée avec succès',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status'  => 'error',
                'message' => 'Erreur lors de la suppression de la consultation: ' . $e->getMessage(),
            ], 500);
        }
    }
}

