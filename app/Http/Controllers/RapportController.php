<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Services\RapportService;
use Illuminate\Http\Request;

use App\Exports\PatientsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ConsultationsExport;
use App\Exports\ExamensExport;
use App\Exports\VaccinationsExport;


class RapportController extends Controller
{
    protected $rapportService;

    public function __construct(RapportService $rapportService)
    {
        $this->rapportService = $rapportService;
    }

    /**
     * Génération du rapport médical complet d'un patient
     */
    public function genererRapportPatient($patientId)
    {
        $patient = Patient::with([
            'consultations',
            'examensLaboratoire',
            'carteUrgence',
            'vaccinations',
            'antecedents'
        ])->findOrFail($patientId);

        $rapport = $this->rapportService->genererPDFPatient($patient);

        return response()->download($rapport->getPath(), 
            "rapport_medical_{$patientId}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Génération de rapport périodique
     */
    public function rapportPeriodique(Request $request)
    {
        $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'type_rapport' => 'required|in:activite,medical,statistique'
        ]);

        $rapport = $this->rapportService->genererRapportPeriodique(
            $request->date_debut,
            $request->date_fin,
            $request->type_rapport
        );

        return response()->json([
            'status' => 'success',
            'data' => $rapport
        ]);
    }

    /**
     * Export des données en Excel
     */
    public function exportDonnees(Request $request)
    {
        $request->validate([
            'type_donnees' => 'required|in:patients,consultations,examens'
        ]);

        return $this->rapportService->exporterExcel(
            $request->type_donnees,
            $request->all()
        );
    }

    public function exporterPatients(Request $request)
    {
        return Excel::download(
            new PatientsExport($request->all()),
            'patients.xlsx'
        );
    }


    public function exporterDonnees(Request $request)
    {
        $type = $request->input('type', 'patients');
        $filtres = $request->except('type');

        $exports = [
            'patients' => PatientsExport::class,
            'consultations' => ConsultationsExport::class,
            'examens' => ExamensExport::class,
            'vaccinations' => VaccinationsExport::class
        ];

        if (!isset($exports[$type])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Type d\'export invalide'
            ], 400);
        }

        $fileName = "{$type}_" . date('Y-m-d') . '.xlsx';
        return Excel::download(new $exports[$type]($filtres), $fileName);
    }
    
}
