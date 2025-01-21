<?php

namespace App\Http\Requests\Medical;

use Illuminate\Foundation\Http\FormRequest;

class VaccinationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'patient_id' => 'required|exists:patients,patient_id',
            'type_vaccin' => 'required|string|max:100',
            'date_vaccination' => 'required|date',
            'rappel_prevu' => 'nullable|date|after:date_vaccination',
            'centre_vaccination' => 'nullable|string|max:255'
        ];
    }
}
