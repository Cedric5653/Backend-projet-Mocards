<?php

namespace App\Http\Requests\Suivie;
use Illuminate\Foundation\Http\FormRequest;

class RendezVousRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'patient_id' => 'required|exists:patients,patient_id',
            'medecin_id' => 'required|exists:users,user_id',
            'date_rdv' => 'required|date|after:now',
            'motif' => 'required|string|max:255',
            'statut' => 'required|in:programmé,confirmé,annulé,terminé'
        ];

        // Règles supplémentaires pour la mise à jour
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = [
                'patient_id' => 'exists:patients,patient_id',
                'medecin_id' => 'exists:users,user_id',
                'date_rdv' => 'date|after:now',
                'motif' => 'string|max:255',
                'statut' => 'in:programmé,confirmé,annulé,terminé'
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'patient_id.required' => 'Le patient est obligatoire',
            'patient_id.exists' => 'Patient invalide',
            'medecin_id.required' => 'Le médecin est obligatoire',
            'medecin_id.exists' => 'Médecin invalide',
            'date_rdv.required' => 'La date du rendez-vous est obligatoire',
            'date_rdv.date' => 'Format de date invalide',
            'date_rdv.after' => 'La date doit être postérieure à maintenant',
            'motif.required' => 'Le motif est obligatoire',
            'motif.string' => 'Le motif doit être une chaîne de caractères',
            'motif.max' => 'Le motif ne doit pas dépasser 255 caractères',
            'statut.required' => 'Le statut est obligatoire',
            'statut.in' => 'Statut invalide'
        ];
    }
}
