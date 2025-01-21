

namespace App\Exports;

use App\Models\Vaccination;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VaccinationsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filtres;

    public function __construct($filtres = [])
    {
        $this->filtres = $filtres;
    }

    public function collection()
    {
        return Vaccination::query()
            ->with('patient')
            ->when(isset($this->filtres['date_debut']), function($query) {
                return $query->whereDate('date_vaccination', '>=', $this->filtres['date_debut']);
            })
            ->when(isset($this->filtres['date_fin']), function($query) {
                return $query->whereDate('date_vaccination', '<=', $this->filtres['date_fin']);
            })
            ->when(isset($this->filtres['type_vaccin']), function($query) {
                return $query->where('type_vaccin', $this->filtres['type_vaccin']);
            })
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Vaccination',
            'Patient',
            'Type Vaccin',
            'Date Vaccination',
            'Rappel Prévu',
            'Centre Vaccination'
        ];
    }

    public function map($vaccination): array
    {
        return [
            $vaccination->vaccination_id,
            $vaccination->patient->nom . ' ' . $vaccination->patient->prenom,
            $vaccination->type_vaccin,
            $vaccination->date_vaccination,
            $vaccination->rappel_prevu ?? 'Aucun rappel prévu',
            $vaccination->centre_vaccination
        ];
    }
}