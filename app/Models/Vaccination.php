<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vaccination extends Model
{
    protected $table = 'vaccinations';
    protected $primaryKey = 'vaccination_id';
    public $timestamps = false;

    protected $fillable = [
        'patient_id', 'type_vaccin', 'date_vaccination',
        'rappel_prevu', 'centre_vaccination'
    ];

    protected $casts = [
        'date_vaccination' => 'date',
        'rappel_prevu' => 'date'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}