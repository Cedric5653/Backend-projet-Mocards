<?php

namespace App\Http\Requests\Dossier;

use Illuminate\Foundation\Http\FormRequest;

class CarteUrgenceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'patient_id' => 'required|exists:patients,patient_id',
            'groupe_sanguin' => 'nullable|string|max:3',
            'allergies' => 'nullable|string',
            'maladies_chroniques' => 'nullable|string',
            'numero_proches' => 'nullable|string|max:100',
            'electrophorese' => 'nullable|string|max:50',
            'maladies_hereditaires' => 'nullable|string',
            'handicap' => 'nullable|string',
            'acute_visuelle' => 'nullable|string',
            'dialyse' => 'boolean',
            'constantes_stables' => 'nullable|string'
        ];
    }
}