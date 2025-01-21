<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarteUrgence extends Model
{
    protected $table = 'carte_urgence';
    protected $primaryKey = 'carte_id';

    protected $fillable = [
        'patient_id', 'groupe_sanguin', 'allergies', 'maladies_chroniques',
        'numero_proches', 'electrophorese', 'maladies_hereditaires',
        'handicap', 'acute_visuelle', 'dialyse', 'constantes_stables'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
