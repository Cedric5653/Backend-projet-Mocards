<?php

namespace App\Http\Requests\Medical;

use Illuminate\Foundation\Http\FormRequest;


class ConsultationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules():array
    {
        return [
            'patient_id' => 'required|exists:patients,patient_id',
            'date_consultation' => 'required|date',
            'type_consultation' => 'required|in:routine,urgence,suivi,specialiste',
            'centre_sante' => 'required|string|max:255',
            'symptomes' => 'nullable|string',
            'diagnostic' => 'nullable|string',
            'prescriptions' => 'nullable|string',
            'observations' => 'nullable|string',
            'hospitalisation' => 'boolean',
            'duree_hospitalisation' => 'nullable|integer|min:0'
        ];
    }
}