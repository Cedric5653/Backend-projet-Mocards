
namespace App\Exports;

use App\Models\Consultation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ConsultationsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filtres;

    public function __construct($filtres = [])
    {
        $this->filtres = $filtres;
    }

    public function collection()
    {
        return Consultation::query()
            ->with(['patient', 'medecin'])
            ->when(isset($this->filtres['date_debut']), function($query) {
                return $query->whereDate('date_consultation', '>=', $this->filtres['date_debut']);
            })
            ->when(isset($this->filtres['date_fin']), function($query) {
                return $query->whereDate('date_consultation', '<=', $this->filtres['date_fin']);
            })
            ->when(isset($this->filtres['type']), function($query) {
                return $query->where('type_consultation', $this->filtres['type']);
            })
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Consultation',
            'Date',
            'Patient',
            'Médecin',
            'Type',
            'Diagnostic',
            'Prescriptions',
            'Hospitalisation',
            'Durée Hospitalisation',
            'Centre de Santé'
        ];
    }

    public function map($consultation): array
    {
        return [
            $consultation->consultation_id,
            $consultation->date_consultation,
            $consultation->patient->nom . ' ' . $consultation->patient->prenom,
            $consultation->medecin->username ?? 'Non assigné',
            $consultation->type_consultation,
            $consultation->diagnostic,
            $consultation->prescriptions,
            $consultation->hospitalisation ? 'Oui' : 'Non',
            $consultation->duree_hospitalisation ?? 'N/A',
            $consultation->centre_sante
        ];
    }
}