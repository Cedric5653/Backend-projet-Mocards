<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class CreatePatientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'required|date',
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'groupe_sanguin' => 'nullable|string|max:3',
            'allergies' => 'nullable|string',
            'maladies_chroniques' => 'nullable|string',
            'personne_contact' => 'nullable|string|max:100',
            'contact_urgence' => 'nullable|string|max:20',
            'localisation_id' => 'required|exists:localisation,localisation_id',
            'lieu_naissance' => 'nullable|string|max:100',
            'profession' => 'nullable|string|max:100',
            'electrophorese' => 'nullable|string|max:50',
            'handicap' => 'nullable|string',
            'vaccination_status' => 'nullable|string',
            'donneur_organes' => 'boolean'
        ];
    }
}




