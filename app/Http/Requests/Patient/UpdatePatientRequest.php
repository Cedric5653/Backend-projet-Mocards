<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $patientId = $this->route('patient');
        
        return [
            'nom' => 'string|max:100',
            'prenom' => 'string|max:100',
            'date_naissance' => 'date',
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100|unique:patients,email,' . $patientId . ',patient_id',
            'groupe_sanguin' => 'nullable|string|max:3',
            'allergies' => 'nullable|string',
            'maladies_chroniques' => 'nullable|string',
            'localisation_id' => 'exists:localisation,localisation_id',
            'lieu_naissance' => 'nullable|string|max:100',
            'profession' => 'nullable|string|max:100',
            'electrophorese' => 'nullable|string|max:50',
            'handicap' => 'nullable|string',
            'vaccination_status' => 'nullable|string',
            'donneur_organes' => 'boolean'
        ];
    }
}