<?php

namespace App\Services;

use PDF;
use Excel;
use Carbon\Carbon;

class RapportService
{
    /**
     * Génération de PDF pour un patient
     */
    public function genererPDFPatient($patient)
    {
        $data = [
            'patient' => $patient,
            'consultations' => $patient->consultations,
            'examens' => $patient->examensLaboratoire,
            'date_generation' => Carbon::now()->format('d/m/Y H:i')
        ];

        $pdf = PDF::loadView('rapports.patient', $data);
        return $pdf;
    }

    /**
     * Génération de rapport périodique
     */
    public function genererRapportPeriodique($dateDebut, $dateFin, $type)
    {
        switch ($type) {
            case 'activite':
                return $this->rapportActivite($dateDebut, $dateFin);
            case 'medical':
                return $this->rapportMedical($dateDebut, $dateFin);
            case 'statistique':
                return $this->rapportStatistique($dateDebut, $dateFin);
        }
    }

    /**
     * Export Excel
     */
    public function exporterExcel($type, $filtres)
    {
        $classMapping = [
            'patients' => \App\Exports\PatientsExport::class,
            'consultations' => \App\Exports\ConsultationsExport::class,
            'examens' => \App\Exports\ExamensExport::class
        ];

        return Excel::download(
            new $classMapping[$type]($filtres),
            "{$type}_export_" . Carbon::now()->format('Y-m-d') . '.xlsx'
        );
    }

    private function rapportActivite($dateDebut, $dateFin)
    {
        // Logique pour rapport d'activité
        return [
            'periode' => [
                'debut' => $dateDebut,
                'fin' => $dateFin
            ],
            'consultations' => [
                'total' => $this->getConsultationsStats($dateDebut, $dateFin),
                'par_type' => $this->getConsultationsParType($dateDebut, $dateFin)
            ],
            'patients' => [
                'nouveaux' => $this->getNouveauxPatients($dateDebut, $dateFin),
                'suivis' => $this->getPatientsSuivis($dateDebut, $dateFin)
            ]
        ];
    }

    private function rapportMedical($dateDebut, $dateFin)
    {
        // Logique pour rapport médical
        return [
            'pathologies' => $this->getPathologiesStats($dateDebut, $dateFin),
            'traitements' => $this->getTraitementsStats($dateDebut, $dateFin),
            'examens' => $this->getExamensStats($dateDebut, $dateFin)
        ];
    }

    private function rapportStatistique($dateDebut, $dateFin)
    {
        // Logique pour rapport statistique
        return [
            'tendances' => $this->getTendances($dateDebut, $dateFin),
            'comparaisons' => $this->getComparaisons($dateDebut, $dateFin),
            'predictions' => $this->getPredictions($dateDebut, $dateFin)
        ];
    }

    private function getConsultationsStats($dateDebut, $dateFin)
    {
        return DB::table('consultations')
            ->whereBetween('date_consultation', [$dateDebut, $dateFin])
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('COUNT(DISTINCT patient_id) as patients_uniques'),
                DB::raw('COUNT(CASE WHEN hospitalisation = 1 THEN 1 END) as hospitalisations'),
                DB::raw('AVG(CASE WHEN hospitalisation = 1 THEN duree_hospitalisation END) as duree_moyenne_hospitalisation')
            )
            ->first();
    }

    private function getConsultationsParType($dateDebut, $dateFin)
    {
        return DB::table('consultations')
            ->whereBetween('date_consultation', [$dateDebut, $dateFin])
            ->select(
                'type_consultation',
                DB::raw('COUNT(*) as total'),
                DB::raw('COUNT(DISTINCT medecin_id) as medecins_impliques')
            )
            ->groupBy('type_consultation')
            ->get();
    }

    private function getNouveauxPatients($dateDebut, $dateFin)
    {
        return DB::table('patients')
            ->whereBetween('created_at', [$dateDebut, $dateFin])
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('COUNT(CASE WHEN donneur_organes = 1 THEN 1 END) as donneurs')
            )
            ->first();
    }

    private function getPatientsSuivis($dateDebut, $dateFin)
    {
        return DB::table('patients')
            ->join('consultations', 'patients.patient_id', '=', 'consultations.patient_id')
            ->whereBetween('consultations.date_consultation', [$dateDebut, $dateFin])
            ->select(
                DB::raw('COUNT(DISTINCT patients.patient_id) as total_suivis'),
                DB::raw('AVG(consultations.duree_consultation) as duree_moyenne_consultation')
            )
            ->first();
    }

    private function getPathologiesStats($dateDebut, $dateFin)
    {
        return DB::table('consultations')
            ->whereBetween('date_consultation', [$dateDebut, $dateFin])
            ->whereNotNull('diagnostic')
            ->select(
                'diagnostic',
                DB::raw('COUNT(*) as occurrences'),
                DB::raw('COUNT(DISTINCT patient_id) as patients_affectes')
            )
            ->groupBy('diagnostic')
            ->orderByDesc('occurrences')
            ->limit(10)
            ->get();
    }

    private function getTraitementsStats($dateDebut, $dateFin)
    {
        // Statistiques sur les traitements prescrits
        return DB::table('consultations')
            ->whereBetween('date_consultation', [$dateDebut, $dateFin])
            ->whereNotNull('prescriptions')
            ->select(
                'prescriptions',
                DB::raw('COUNT(*) as frequence')
            )
            ->groupBy('prescriptions')
            ->orderByDesc('frequence')
            ->limit(10)
            ->get();
    }

    private function getExamensStats($dateDebut, $dateFin)
    {
        return DB::table('examens_laboratoire')
            ->whereBetween('date_examen', [$dateDebut, $dateFin])
            ->select(
                'type_examen',
                DB::raw('COUNT(*) as total'),
                DB::raw('COUNT(DISTINCT patient_id) as patients')
            )
            ->groupBy('type_examen')
            ->get();
    }
}