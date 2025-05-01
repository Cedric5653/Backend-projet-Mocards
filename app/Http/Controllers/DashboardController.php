<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\RendezVous;
use App\Models\Consultation;
use App\Http\Controllers\StatistiquesController;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Renvoyer les statistiques globales en déléguant au StatistiquesController.
     */
    public function stats(Request $request)
    {
        try {
            $statController = new StatistiquesController();
            $response = $statController->index();
            $data = $response->getData(true);
            return response()->json([
                'status' => 'success',
                'data'   => $data['data'] ?? []
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans DashboardController::stats: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Erreur lors de la récupération des statistiques'
            ], 500);
        }
    }

    /**
     * Renvoyer des activités récentes issues des consultations et des rendez‑vous.
     */
    public function activities(Request $request)
    {
        try {
            // Récupérer les 5 dernières consultations
            $recentConsultations = Consultation::with(['patient', 'medecin'])
                ->orderBy('date_consultation', 'desc')
                ->take(5)
                ->get()
                ->map(function ($consultation) {
                    return [
                        'id'        => $consultation->consultation_id,
                        'title'     => 'Consultation : ' . ($consultation->diagnostic ?? 'Sans diagnostic'),
                        'type'      => 'consultation',
                        'timestamp' => $consultation->date_consultation->toDateTimeString(),
                        'details'   => [
                            'patient' => optional($consultation->patient)->nom . ' ' . optional($consultation->patient)->prenom,
                            'medecin' => optional($consultation->medecin)->nom . ' ' . optional($consultation->medecin)->prenom,
                        ],
                    ];
                });

            // Récupérer les 5 derniers rendez‑vous
            $recentRendezVous = RendezVous::with(['patient', 'medecin'])
                ->orderBy('date_rdv', 'desc')
                ->take(5)
                ->get()
                ->map(function ($rdv) {
                    return [
                        'id'        => $rdv->rdv_id,
                        'title'     => 'Rendez‑vous : ' . ($rdv->motif ?? 'Sans motif'),
                        'type'      => 'appointment',
                        'timestamp' => Carbon::parse($rdv->date_rdv)->toDateTimeString(),
                        'details'   => [
                            'patient' => optional($rdv->patient)->nom . ' ' . optional($rdv->patient)->prenom,
                            'medecin' => optional($rdv->medecin)->nom . ' ' . optional($rdv->medecin)->prenom,
                        ],
                    ];
                });

            // Fusionner et trier par timestamp décroissant
            $activities = $recentConsultations->merge($recentRendezVous)
                ->sortByDesc('timestamp')
                ->values();

            return response()->json([
                'status' => 'success',
                'data'   => $activities,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans DashboardController::activities: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Erreur lors de la récupération des activités'
            ], 500);
        }
    }

    /**
     * Renvoyer les rendez‑vous à venir.
     */
    public function appointments(Request $request)
    {
        try {
            $appointments = RendezVous::with(['patient', 'medecin'])
                ->where('date_rdv', '>=', Carbon::now())
                ->orderBy('date_rdv', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data'   => $appointments,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans DashboardController::appointments: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Erreur lors de la récupération des rendez-vous'
            ], 500);
        }
    }

    /**
     * Renvoyer les alertes en utilisant la méthode alertesSanitaires du StatistiquesController.
     */
    public function alerts(Request $request)
    {
        try {
            $statController = new StatistiquesController();
            $response = $statController->alertesSanitaires();
            $data = $response->getData(true);
            return response()->json([
                'status' => 'success',
                'data'   => $data['data'] ?? []
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans DashboardController::alerts: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Erreur lors de la récupération des alertes'
            ], 500);
        }
    }
}
