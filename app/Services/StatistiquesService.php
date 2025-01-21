<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Consultation;
use App\Models\ExamenLaboratoire;
use App\Models\Localisation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class StatistiquesService
{
    /**
     * Obtenir les statistiques globales
     */
    public function getStatistiquesGlobales()
    {
        return Cache::remember('stats.globales', 3600, function () {
            return [
                'patients' => $this->getStatistiquesPatients(),
                'consultations' => $this->getStatistiquesConsultations(),
                'examens' => $this->getStatistiquesExamens(),
                'localisation' => $this->getStatistiquesLocalisation()
            ];
        });
    }

    /**
     * Statistiques des patients
     */
    private function getStatistiquesPatients()
    {
        return [
            'total' => Patient::count(),
            'nouveau_mois' => Patient::whereMonth('created_at', now()->month)->count(),
            'par_groupe_sanguin' => Patient::select('groupe_sanguin', DB::raw('count(*) as total'))
                ->whereNotNull('groupe_sanguin')
                ->groupBy('groupe_sanguin')
                ->get(),
            'donneurs_organes' => Patient::where('donneur_organes', true)->count()
        ];
    }

    /**
     * Statistiques des consultations
     */
    private function getStatistiquesConsultations()
    {
        return [
            'total' => Consultation::count(),
            'par_type' => Consultation::select('type_consultation', DB::raw('count(*) as total'))
                ->groupBy('type_consultation')
                ->get(),
            'urgences_mois' => Consultation::where('type_consultation', 'urgence')
                ->whereMonth('date_consultation', now()->month)
                ->count(),
            'hospitalisations' => Consultation::where('hospitalisation', true)
                ->count()
        ];
    }

    /**
     * Statistiques des examens
     */
    private function getStatistiquesExamens()
    {
        return [
            'total' => ExamenLaboratoire::count(),
            'par_type' => ExamenLaboratoire::select('type_examen', DB::raw('count(*) as total'))
                ->groupBy('type_examen')
                ->get(),
            'mois_courant' => ExamenLaboratoire::whereMonth('date_examen', now()->month)
                ->count()
        ];
    }

    /**
     * Statistiques par localisation
     */
    public function getStatistiquesParLocalisation()
    {
        return Localisation::withCount('patients')
            ->with(['patients' => function($query) {
                $query->select('localisation_id')
                    ->selectRaw('COUNT(DISTINCT consultations.consultation_id) as total_consultations')
                    ->leftJoin('consultations', 'patients.patient_id', '=', 'consultations.patient_id')
                    ->groupBy('localisation_id');
            }])
            ->get();
    }

    /**
     * Statistiques des pathologies
     */
    public function getStatistiquesPathologies()
    {
        return Cache::remember('stats.pathologies', 3600, function () {
            return Consultation::select('diagnostic', DB::raw('count(*) as total'))
                ->whereNotNull('diagnostic')
                ->groupBy('diagnostic')
                ->orderByDesc('total')
                ->limit(10)
                ->get();
        });
    }

    /**
     * Évolution temporelle
     */
    public function getEvolutionTemporelle($mois = 12)
    {
        $debut = now()->subMonths($mois);
        
        return [
            'consultations' => $this->getEvolutionConsultations($debut),
            'nouveaux_patients' => $this->getEvolutionNouveauxPatients($debut),
            'examens' => $this->getEvolutionExamens($debut)
        ];
    }

    /**
     * Méthodes privées pour l'évolution
     */
    private function getEvolutionConsultations($debut)
    {
        return Consultation::select(
            DB::raw('DATE_FORMAT(date_consultation, "%Y-%m") as mois'),
            DB::raw('COUNT(*) as total')
        )
        ->where('date_consultation', '>=', $debut)
        ->groupBy('mois')
        ->orderBy('mois')
        ->get();
    }

    private function getEvolutionNouveauxPatients($debut)
    {
        return Patient::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mois'),
            DB::raw('COUNT(*) as total')
        )
        ->where('created_at', '>=', $debut)
        ->groupBy('mois')
        ->orderBy('mois')
        ->get();
    }

    private function getEvolutionExamens($debut)
    {
        return ExamenLaboratoire::select(
            DB::raw('DATE_FORMAT(date_examen, "%Y-%m") as mois'),
            DB::raw('COUNT(*) as total')
        )
        ->where('date_examen', '>=', $debut)
        ->groupBy('mois')
        ->orderBy('mois')
        ->get();
    }
}