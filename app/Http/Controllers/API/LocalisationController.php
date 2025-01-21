<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Localisation;
use Illuminate\Http\Request;
use App\Http\Requests\LocalisationRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class LocalisationController extends Controller
{
   /**
    * Liste des localisations avec filtres et cache
    */
   public function index(Request $request)
   {
       $cacheKey = 'localisations_' . md5($request->fullUrl());

       return Cache::remember($cacheKey, 3600, function () use ($request) {
           $query = Localisation::query();

           // Filtres
           if ($request->has('region')) {
               $query->where('region', 'like', '%' . $request->region . '%');
           }
           if ($request->has('province')) {
               $query->where('province', 'like', '%' . $request->province . '%');
           }
           if ($request->has('ville')) {
               $query->where('ville', 'like', '%' . $request->ville . '%');
           }
           if ($request->has('district_sanitaire')) {
               $query->where('district_sanitaire', 'like', '%' . $request->district_sanitaire . '%');
           }

           // Relations et comptage
           $query->withCount(['patients']);

           $localisations = $query->paginate($request->input('per_page', 15));

           return response()->json([
               'status' => 'success',
               'data' => $localisations
           ]);
       });
   }

   /**
    * Créer une nouvelle localisation
    */
   public function store(LocalisationRequest $request)
   {
       try {
           DB::beginTransaction();

           // Vérifier si la localisation existe déjà
           $exists = Localisation::where('region', $request->region)
               ->where('province', $request->province)
               ->where('ville', $request->ville)
               ->where('district_sanitaire', $request->district_sanitaire)
               ->exists();

           if ($exists) {
               return response()->json([
                   'status' => 'error',
                   'message' => 'Cette localisation existe déjà'
               ], 422);
           }

           $localisation = Localisation::create($request->validated());

           // Invalider le cache des localisations
           Cache::tags(['localisations'])->flush();

           DB::commit();

           return response()->json([
               'status' => 'success',
               'message' => 'Localisation créée avec succès',
               'data' => $localisation
           ], 201);

       } catch (\Exception $e) {
           DB::rollback();
           return response()->json([
               'status' => 'error',
               'message' => 'Erreur lors de la création de la localisation',
               'error' => $e->getMessage()
           ], 500);
       }
   }

   /**
    * Afficher une localisation spécifique avec ses statistiques
    */
   public function show($id)
   {
       $localisation = Localisation::with(['patients'])
           ->withCount(['patients'])
           ->findOrFail($id);

       // Statistiques détaillées
       $stats = [
           'total_patients' => $localisation->patients_count,
           'patients_par_type' => $this->getPatientStats($localisation->patient_id),
           'consultations' => $this->getConsultationStats($localisation->patient_id)
       ];

       return response()->json([
           'status' => 'success',
           'data' => [
               'localisation' => $localisation,
               'statistiques' => $stats
           ]
       ]);
   }

   /**
    * Mise à jour d'une localisation
    */
   public function update(LocalisationRequest $request, $id)
   {
       try {
           DB::beginTransaction();

           $localisation = Localisation::findOrFail($id);

           // Vérifier si la mise à jour créerait un doublon
           $exists = Localisation::where('region', $request->get('region', $localisation->region))
               ->where('province', $request->get('province', $localisation->province))
               ->where('ville', $request->get('ville', $localisation->ville))
               ->where('district_sanitaire', $request->get('district_sanitaire', $localisation->district_sanitaire))
               ->where('localisation_id', '!=', $id)
               ->exists();

           if ($exists) {
               return response()->json([
                   'status' => 'error',
                   'message' => 'Cette combinaison de localisation existe déjà'
               ], 422);
           }

           $localisation->update($request->validated());

           // Invalider le cache
           Cache::tags(['localisations'])->flush();

           DB::commit();

           return response()->json([
               'status' => 'success',
               'message' => 'Localisation mise à jour avec succès',
               'data' => $localisation
           ]);

       } catch (\Exception $e) {
           DB::rollback();
           return response()->json([
               'status' => 'error',
               'message' => 'Erreur lors de la mise à jour de la localisation',
               'error' => $e->getMessage()
           ], 500);
       }
   }

   /**
    * Supprimer une localisation
    */
   public function destroy($id)
   {
       try {
           DB::beginTransaction();

           $localisation = Localisation::findOrFail($id);

           // Vérifier si la localisation est utilisée
           if ($localisation->patients()->exists()) {
               return response()->json([
                   'status' => 'error',
                   'message' => 'Impossible de supprimer cette localisation car elle est associée à des patients'
               ], 422);
           }

           $localisation->delete();

           // Invalider le cache
           Cache::tags(['localisations'])->flush();

           DB::commit();

           return response()->json([
               'status' => 'success',
               'message' => 'Localisation supprimée avec succès'
           ]);

       } catch (\Exception $e) {
           DB::rollback();
           return response()->json([
               'status' => 'error',
               'message' => 'Erreur lors de la suppression de la localisation',
               'error' => $e->getMessage()
           ], 500);
       }
   }

   /**
    * Obtenir les statistiques des patients par type
    */
   private function getPatientStats($localisationId)
   {
       return DB::table('patients')
           ->where('localisation_id', $localisationId)
           ->select(
               DB::raw('COUNT(*) as total'),
               DB::raw('COUNT(CASE WHEN donneur_organes = 1 THEN 1 END) as donneurs'),
               DB::raw('COUNT(CASE WHEN handicap IS NOT NULL THEN 1 END) as handicapes')
           )
           ->first();
   }

   /**
    * Obtenir les statistiques des consultations
    */
   private function getConsultationStats($localisationId)
   {
       return DB::table('consultations')
           ->join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
           ->where('patients.localisation_id', $localisationId)
           ->select(
               DB::raw('COUNT(*) as total'),
               DB::raw("COUNT(CASE WHEN type_consultation = 'urgence' THEN 1 END) as urgences"),
               DB::raw("COUNT(CASE WHEN type_consultation = 'routine' THEN 1 END) as routines")
           )
           ->first();
   }
}