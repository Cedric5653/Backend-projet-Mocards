<?php

namespace App\Http\Requests\Dossier;

use Illuminate\Foundation\Http\FormRequest;

class CarnetMaternitéRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'patient_id' => 'required|exists:patients,patient_id',
            'date_debut_grossesse' => 'required|date',
            'nombre_grossesses' => 'nullable|integer|min:0',
            'nombre_accouchements' => 'nullable|integer|min:0',
            'complications' => 'nullable|string',
            'groupage_sanguin' => 'nullable|string|max:5',
            'statut' => 'required|in:vierge,en_cours,termine'
        ];
    }
}