<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CarnetMaternite;
use App\Http\Requests\API\Dossier\CarnetMaternitéRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarnetMaternitéController extends Controller
{
    /**
     * Liste des carnets de maternité
     */
    public function index(Request $request)
    {
        $query = CarnetMaternite::with('patient');

        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        $carnets = $query->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $carnets
        ]);
    }

    /**
     * Création d'un nouveau carnet de maternité
     */
    public function store(CarnetMaternitéRequest $request)
    {
        DB::beginTransaction();
        try {
            // Vérifier si un carnet existe déjà pour cette patiente
            $existingCarnet = CarnetMaternite::where('patient_id', $request->patient_id)
                ->where('statut', 'en_cours')
                ->first();

            if ($existingCarnet) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Un carnet de maternité en cours existe déjà pour cette patiente'
                ], 422);
            }

            $carnet = CarnetMaternite::create($request->validated());

            // Créer les enregistrements associés si nécessaire
            if ($request->has('consultations_initiales')) {
                // Logique pour les consultations initiales
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Carnet de maternité créé avec succès',
                'data' => $carnet
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la création du carnet de maternité',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher un carnet de maternité spécifique
     */
    public function show($id)
    {
        $carnet = CarnetMaternite::with(['patient' => function($query) {
            $query->select('patient_id', 'nom', 'prenom', 'date_naissance');
        }])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $carnet
        ]);
    }

    /**
     * Mise à jour d'un carnet de maternité
     */
    public function update(CarnetMaternitéRequest $request, $id)
    {
        $carnet = CarnetMaternite::findOrFail($id);

        DB::beginTransaction();
        try {
            $carnet->update($request->validated());

            // Mise à jour des informations associées
            if ($request->has('complications')) {
                // Mettre à jour les complications
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Carnet de maternité mis à jour avec succès',
                'data' => $carnet
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour du carnet de maternité',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clôturer un carnet de maternité
     */
    public function terminer($id)
    {
        $carnet = CarnetMaternite::findOrFail($id);

        if ($carnet->statut === 'termine') {
            return response()->json([
                'status' => 'error',
                'message' => 'Ce carnet est déjà terminé'
            ], 422);
        }

        $carnet->update([
            'statut' => 'termine'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Carnet de maternité clôturé avec succès',
            'data' => $carnet
        ]);
    }

    /**
     * Statistiques des carnets de maternité
     */
    public function statistics()
    {
        $stats = [
            'total' => CarnetMaternite::count(),
            'en_cours' => CarnetMaternite::where('statut', 'en_cours')->count(),
            'termine' => CarnetMaternite::where('statut', 'termine')->count(),
            'par_mois' => CarnetMaternite::select(
                DB::raw('MONTH(date_debut_grossesse) as mois'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('mois')
            ->get()
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }
}