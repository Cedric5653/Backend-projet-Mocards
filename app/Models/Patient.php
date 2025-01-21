<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Patient extends Model
{
    protected $table = 'patients';
    protected $primaryKey = 'patient_id';

    protected $fillable = [
        'nom', 'prenom', 'date_naissance', 'adresse', 'telephone',
        'email', 'groupe_sanguin', 'allergies', 'maladies_chroniques',
        'personne_contact', 'contact_urgence', 'assurance_medicale',
        'numero_securite_sociale', 'localisation_id', 'lieu_naissance',
        'profession', 'electrophorese', 'handicap', 'vaccination_status',
        'donneur_organes'
    ];

    public function carteUrgence()
    {
        return $this->hasOne(CarteUrgence::class, 'patient_id');
    }

    public function carnetNumerique()
    {
        return $this->hasMany(CarnetNumerique::class, 'patient_id');
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'patient_id');
    }

    public function localisation()
    {
        return $this->belongsTo(Localisation::class, 'localisation_id');
    }

    public function examensLaboratoire()
    {
        return $this->hasMany(ExamenLaboratoire::class, 'patient_id');
    }

    public function carnetMaternite()
    {
        return $this->hasOne(CarnetMaternite::class, 'patient_id');
    }

    public function antecedents()
    {
        return $this->hasMany(AntecedentMedical::class, 'patient_id');
    }

    public function vaccinations()
    {
        return $this->hasMany(Vaccination::class, 'patient_id');
    }
}