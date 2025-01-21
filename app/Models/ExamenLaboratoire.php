<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamenLaboratoire extends Model
{
    protected $table = 'examens_laboratoire';
    protected $primaryKey = 'examen_id';
    public $timestamps = false;

    protected $fillable = [
        'patient_id', 'type_examen', 'date_examen',
        'resultat', 'centre_examen', 'medecin_id',
        'document_id'
    ];

    protected $casts = [
        'date_examen' => 'date'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }

    public function document()
    {
        return $this->belongsTo(DocumentMedical::class, 'document_id');
    }
}