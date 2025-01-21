<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\RendezVous;
use App\Http\Requests\Suivie\RendezVousRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RendezVousController extends Controller
{
    /**
     * Liste des rendez-vous avec filtres avancés
     */
    public function index(Request $request)
    {
        $query = RendezVous::with(['patient', 'medecin']);

        // Filtres
        if ($request->has('medecin_id')) {
            $query->where('medecin_id', $request->medecin_id);
        }
        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }
        if ($request->has('date_debut') && $request->has('date_fin')) {
            $query->whereBetween('date_rdv', [$request->date_debut, $request->date_fin]);
        }
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        // Vérification des conflits horaires
        if ($request->has('check_conflicts')) {
            $query->where(function($q) {
                $q->where('statut', '!=', 'annulé')
                  ->where('date_rdv', '>=', now());
            });
        }

        $rendezVous = $query->orderBy('date_rdv', 'asc')
                           ->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $rendezVous
        ]);
    }

    /**
     * Création d'un nouveau rendez-vous avec vérification des conflits
     */
    public function store(RendezVousRequest $request)
    {
        try {
            DB::beginTransaction();

            // Vérifier les conflits horaires
            $conflit = $this->verifierConflitsHoraires(
                $request->medecin_id,
                $request->date_rdv,
                null
            );

            if ($conflit) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Conflit horaire détecté'
                ], 422);
            }

            $rendezVous = RendezVous::create($request->validated());

            // Notifications (à implémenter selon vos besoins)
            // event(new RendezVousCreated($rendezVous));

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Rendez-vous créé avec succès',
                'data' => $rendezVous->load(['patient', 'medecin'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la création du rendez-vous',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher les détails d'un rendez-vous
     */
    public function show($id)
    {
        $rendezVous = RendezVous::with(['patient', 'medecin'])->findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $rendezVous
        ]);
    }

    /**
     * Mise à jour d'un rendez-vous
     */
    public function update(RendezVousRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $rendezVous = RendezVous::findOrFail($id);

            // Vérifier si le rendez-vous peut être modifié
            if ($rendezVous->statut === 'terminé') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Impossible de modifier un rendez-vous terminé'
                ], 422);
            }

            // Vérifier les conflits horaires si la date change
            if ($request->has('date_rdv') && $request->date_rdv != $rendezVous->date_rdv) {
                $conflit = $this->verifierConflitsHoraires(
                    $request->get('medecin_id', $rendezVous->medecin_id),
                    $request->date_rdv,
                    $id
                );

                if ($conflit) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Conflit horaire détecté'
                    ], 422);
                }
            }

            $rendezVous->update($request->validated());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Rendez-vous mis à jour avec succès',
                'data' => $rendezVous->load(['patient', 'medecin'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour du rendez-vous',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer/Annuler un rendez-vous
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $rendezVous = RendezVous::findOrFail($id);

            if ($rendezVous->statut === 'terminé') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Impossible de supprimer un rendez-vous terminé'
                ], 422);
            }

            // Marquer comme annulé au lieu de supprimer
            $rendezVous->update(['statut' => 'annulé']);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Rendez-vous annulé avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de l\'annulation du rendez-vous',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier les conflits horaires
     */
    private function verifierConflitsHoraires($medecinId, $dateRdv, $excludeId = null)
    {
        $date = Carbon::parse($dateRdv);
        $debutPlage = $date->copy()->subMinutes(30);
        $finPlage = $date->copy()->addMinutes(30);

        $query = RendezVous::where('medecin_id', $medecinId)
            ->where('statut', '!=', 'annulé')
            ->whereBetween('date_rdv', [$debutPlage, $finPlage]);

        if ($excludeId) {
            $query->where('rdv_id', '!=', $excludeId);
        }

        return $query->exists();
    }
}