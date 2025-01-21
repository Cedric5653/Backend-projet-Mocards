<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\Localisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class StatistiquesController extends Controller
{
    /**
     * Statistiques globales du système
     */
    public function index()
    {
        // Utilisation du cache pour optimiser les performances
        return Cache::remember('statistiques_globales', 3600, function () {
            $stats = [
                'patients' => [
                    'total' => Patient::count(),
                    'nouveaux_mois' => Patient::whereMonth('created_at', Carbon::now()->month)->count(),
                    'par_sexe' => $this->getPatientParSexe(),
                    'par_age' => $this->getPatientParAge()
                ],
                'consultations' => [
                    'total' => Consultation::count(),
                    'ce_mois' => Consultation::whereMonth('date_consultation', Carbon::now()->month)->count(),
                    'par_type' => $this->getConsultationParType(),
                    'evolution' => $this->getEvolutionConsultations()
                ],
                'urgences' => [
                    'total' => Consultation::where('type_consultation', 'urgence')->count(),
                    'aujourdhui' => $this->getUrgencesAujourdhui()
                ]
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);
        });
    }

    /**
     * Statistiques par région
     */
    public function parRegion()
    {
        $statistiques = Localisation::withCount(['patients'])
            ->with(['patients' => function($query) {
                $query->select('localisation_id')
                    ->selectRaw('COUNT(*) as total_consultations')
                    ->join('consultations', 'patients.patient_id', '=', 'consultations.patient_id')
                    ->groupBy('localisation_id');
            }])
            ->get()
            ->map(function ($localisation) {
                return [
                    'region' => $localisation->region,
                    'nombre_patients' => $localisation->patients_count,
                    'consultations' => $localisation->patients->first()->total_consultations ?? 0,
                    'details' => [
                        'provinces' => $this->getStatistiquesProvinces($localisation->localisation_id),
                        'pathologies' => $this->getPathologiesFrequentes($localisation->localisation_id)
                    ]
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $statistiques
        ]);
    }

    /**
     * Statistiques médicales
     */
    public function statistiquesMedicales()
    {
        $statistiques = [
            'maladies_frequentes' => $this->getMaladiesFrequentes(),
            'types_consultations' => $this->getTypesConsultations(),
            'vaccinations' => $this->getStatistiquesVaccinations(),
            'tendances' => [
                'mensuelle' => $this->getTendancesMensuelles(),
                'pathologies' => $this->getTendancesPathologies()
            ]
        ];

        return response()->json([
            'status' => 'success',
            'data' => $statistiques
        ]);
    }

    /**
     * Méthodes privées pour le calcul des statistiques
     */
    private function getPatientParSexe()
    {
        return Patient::select('sexe', DB::raw('count(*) as total'))
            ->groupBy('sexe')
            ->get();
    }

    private function getPatientParAge()
    {
        return Patient::select(DB::raw('
            CASE 
                WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) < 18 THEN "0-17"
                WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) < 30 THEN "18-29"
                WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) < 50 THEN "30-49"
                ELSE "50+"
            END as tranche_age'
        ), DB::raw('count(*) as total'))
        ->groupBy('tranche_age')
        ->get();
    }

    private function getConsultationParType()
    {
        return Consultation::select('type_consultation', DB::raw('count(*) as total'))
            ->groupBy('type_consultation')
            ->get();
    }

    private function getEvolutionConsultations()
    {
        return Consultation::select(
            DB::raw('DATE_FORMAT(date_consultation, "%Y-%m") as mois'),
            DB::raw('count(*) as total')
        )
        ->whereYear('date_consultation', Carbon::now()->year)
        ->groupBy('mois')
        ->orderBy('mois')
        ->get();
    }

    private function getUrgencesAujourdhui()
    {
        return Consultation::where('type_consultation', 'urgence')
            ->whereDate('date_consultation', Carbon::today())
            ->count();
    }

    private function getTypesConsultations()
    {
        return Consultation::select(
            'type_consultation',
            DB::raw('count(*) as total'),
            DB::raw('count(distinct patient_id) as patients_uniques')
        )
        ->groupBy('type_consultation')
        ->get();
    }

    private function getStatistiquesProvinces($localisationId)
    {
        return DB::table('patients')
            ->join('localisations', 'patients.localisation_id', '=', 'localisations.localisation_id')
            ->join('consultations', 'patients.patient_id', '=', 'consultations.patient_id')
            ->where('localisations.localisation_id', $localisationId)
            ->select(
                'localisations.province',
                DB::raw('COUNT(DISTINCT patients.patient_id) as total_patients'),
                DB::raw('COUNT(consultations.consultation_id) as total_consultations'),
                DB::raw('COUNT(CASE WHEN consultations.type_consultation = "urgence" THEN 1 END) as urgences')
            )
            ->groupBy('localisations.province')
            ->get();
    }
    
    private function getPathologiesFrequentes($localisationId)
    {
        return DB::table('consultations')
            ->join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
            ->where('patients.localisation_id', $localisationId)
            ->select(
                'diagnostic',
                DB::raw('COUNT(*) as total_cas'),
                DB::raw('COUNT(DISTINCT patients.patient_id) as patients_uniques')
            )
            ->whereNotNull('diagnostic')
            ->groupBy('diagnostic')
            ->orderByDesc('total_cas')
            ->limit(10)
            ->get();
    }
    
    private function getMaladiesFrequentes()
    {
        return DB::table('consultations')
            ->select(
                'diagnostic',
                DB::raw('COUNT(*) as occurrences'),
                DB::raw('COUNT(DISTINCT patient_id) as patients_affectes'),
                DB::raw('AVG(CASE WHEN hospitalisation = 1 THEN 1 ELSE 0 END) * 100 as taux_hospitalisation')
            )
            ->whereNotNull('diagnostic')
            ->groupBy('diagnostic')
            ->orderByDesc('occurrences')
            ->limit(15)
            ->get();
    }
    
    private function getStatistiquesVaccinations()
    {
        return [
            'total' => DB::table('vaccinations')->count(),
            'par_type' => DB::table('vaccinations')
                ->select('type_vaccin', DB::raw('COUNT(*) as total'))
                ->groupBy('type_vaccin')
                ->get(),
            'couverture' => DB::table('patients')
                ->select(
                    DB::raw('COUNT(*) as total_patients'),
                    DB::raw('COUNT(CASE WHEN vaccination_status IS NOT NULL THEN 1 END) as vaccines')
                )
                ->first(),
            'rappels_prevus' => DB::table('vaccinations')
                ->where('rappel_prevu', '>=', now())
                ->count()
        ];
    }
    
    private function getTendancesMensuelles()
    {
        $derniersMois = 12;
        return DB::table('consultations')
            ->select(
                DB::raw('DATE_FORMAT(date_consultation, "%Y-%m") as mois'),
                DB::raw('COUNT(*) as total_consultations'),
                DB::raw('COUNT(CASE WHEN type_consultation = "urgence" THEN 1 END) as urgences'),
                DB::raw('COUNT(DISTINCT patient_id) as patients_uniques')
            )
            ->where('date_consultation', '>=', now()->subMonths($derniersMois))
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();
    }
    
    private function getTendancesPathologies()
    {
        return DB::table('consultations')
            ->select(
                DB::raw('DATE_FORMAT(date_consultation, "%Y-%m") as mois'),
                'diagnostic',
                DB::raw('COUNT(*) as cas')
            )
            ->whereNotNull('diagnostic')
            ->where('date_consultation', '>=', now()->subMonths(6))
            ->groupBy('mois', 'diagnostic')
            ->orderBy('mois')
            ->get()
            ->groupBy('diagnostic');
    }


    // Nouvelles méthodes dans StatistiquesController

public function rapportHebdomadaire()
{
    $debut = now()->startOfWeek();
    $fin = now()->endOfWeek();

    return response()->json([
        'status' => 'success',
        'data' => [
            'periode' => [
                'debut' => $debut->format('Y-m-d'),
                'fin' => $fin->format('Y-m-d')
            ],
            'consultations' => $this->getStatistiquesHebdomadaires($debut, $fin),
            'nouveaux_patients' => $this->getNouveauxPatients($debut, $fin),
            'urgences' => $this->getUrgencesHebdomadaires($debut, $fin)
        ]
    ]);
}

public function performanceMedecins()
{
    return response()->json([
        'status' => 'success',
        'data' => [
            'consultations_par_medecin' => $this->getConsultationsParMedecin(),
            'temps_moyen_consultation' => $this->getTempsMoyenConsultation(),
            'taux_satisfaction' => $this->getTauxSatisfaction()
        ]
    ]);
}

public function alertesSanitaires()
{
    return response()->json([
        'status' => 'success',
        'data' => [
            'maladies_en_hausse' => $this->getMaladiesEnHausse(),
            'zones_risque' => $this->getZonesRisque(),
            'recommandations' => $this->getRecommandations()
        ]
    ]);
}
}



