
namespace App\Exports;

use App\Models\ExamenLaboratoire;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExamensExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filtres;

    public function __construct($filtres = [])
    {
        $this->filtres = $filtres;
    }

    public function collection()
    {
        return ExamenLaboratoire::query()
            ->with(['patient', 'medecin'])
            ->when(isset($this->filtres['date_debut']), function($query) {
                return $query->whereDate('date_examen', '>=', $this->filtres['date_debut']);
            })
            ->when(isset($this->filtres['date_fin']), function($query) {
                return $query->whereDate('date_examen', '<=', $this->filtres['date_fin']);
            })
            ->when(isset($this->filtres['type_examen']), function($query) {
                return $query->where('type_examen', $this->filtres['type_examen']);
            })
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Examen',
            'Date',
            'Patient',
            'Type Examen',
            'Résultat',
            'Centre Examen',
            'Médecin Prescripteur',
            'Document Associé'
        ];
    }

    public function map($examen): array
    {
        return [
            $examen->examen_id,
            $examen->date_examen,
            $examen->patient->nom . ' ' . $examen->patient->prenom,
            $examen->type_examen,
            $examen->resultat,
            $examen->centre_examen,
            $examen->medecin->username ?? 'Non assigné',
            $examen->document_id ? 'Oui' : 'Non'
        ];
    }
}
