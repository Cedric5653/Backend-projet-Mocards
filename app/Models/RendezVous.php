<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    protected $table = 'rendez_vous';
    protected $primaryKey = 'rdv_id';
    protected $dates = ['date_rdv'];

    protected $fillable = [
        'patient_id', 'medecin_id', 'date_rdv',
        'motif', 'statut'
    ];

    protected $casts = [
        'date_rdv' => 'datetime'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }
}