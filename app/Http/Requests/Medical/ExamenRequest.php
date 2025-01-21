<?php

namespace App\Http\Requests\Medical;

use Illuminate\Foundation\Http\FormRequest;

class ExamenRequest extends FormRequest
{
    public function authorize():bool
    {
        return true;
    }

    public function rules():array
    {
        return [
            'patient_id' => 'required|exists:patients,patient_id',
            'type_examen' => 'required|in:LMB,radio,echo,autre',
            'date_examen' => 'required|date',
            'resultat' => 'nullable|string',
            'centre_examen' => 'required|string|max:255',
            'medecin_id' => 'required|exists:users,user_id',
            'document_id' => 'nullable|exists:documents_medicaux,document_id'
        ];
    }
}