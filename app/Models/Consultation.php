<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $table = 'consultations';
    protected $primaryKey = 'consultation_id';
    public $timestamps = false;

    protected $fillable = [
        'patient_id', 'date_consultation', 'type_consultation',
        'centre_sante', 'symptomes', 'diagnostic', 'prescriptions',
        'observations', 'hospitalisation', 'duree_hospitalisation',
        'medecin_id'
    ];

    protected $casts = [
        'hospitalisation' => 'boolean',
        'date_consultation' => 'date'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }

    public function carnetNumerique()
    {
        return $this->hasOne(CarnetNumerique::class, 'consultation_id');
    }
}