
namespace App\Exports;

use App\Models\Patient;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PatientsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filtres;

    public function __construct($filtres = [])
    {
        $this->filtres = $filtres;
    }

    public function collection()
    {
        return Patient::query()
            ->when(isset($this->filtres['nom']), function($query) {
                return $query->where('nom', 'like', '%' . $this->filtres['nom'] . '%');
            })
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nom',
            'Prénom',
            'Date de Naissance',
            'Téléphone',
            'Email',
            'Groupe Sanguin',
        ];
    }

    public function map($patient): array
    {
        return [
            $patient->patient_id,
            $patient->nom,
            $patient->prenom,
            $patient->date_naissance,
            $patient->telephone,
            $patient->email,
            $patient->groupe_sanguin,
        ];
    }
}