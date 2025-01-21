<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarnetNumerique extends Model
{
    protected $table = 'carnet_numerique';
    protected $primaryKey = 'carnet_id';

    protected $fillable = [
        'patient_id', 'type_enregistrement', 'description',
        'date_enregistrement', 'medecin_id', 'hôpital',
        'consultation_id', 'last_modified_by'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class, 'consultation_id');
    }

    public function lastModifiedBy()
    {
        return $this->belongsTo(User::class, 'last_modified_by');
    }
}