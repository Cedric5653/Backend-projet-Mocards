<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarnetMaternite extends Model
{
    protected $table = 'carnet_maternite';
    protected $primaryKey = 'maternite_id';
    public $timestamps = false;

    protected $fillable = [
        'patient_id', 'date_debut_grossesse', 'nombre_grossesses',
        'nombre_accouchements', 'complications', 'groupage_sanguin', 
        'statut'
    ];

    protected $casts = [
        'date_debut_grossesse' => 'date'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}