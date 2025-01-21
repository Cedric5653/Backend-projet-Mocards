<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class LocalisationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'region' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'ville' => 'required|string|max:100',
            'district_sanitaire' => 'required|string|max:100'
        ];

        // Règles pour la mise à jour
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = [
                'region' => 'string|max:100',
                'province' => 'string|max:100',
                'ville' => 'string|max:100',
                'district_sanitaire' => 'string|max:100'
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'region.required' => 'La région est obligatoire',
            'region.string' => 'La région doit être une chaîne de caractères',
            'region.max' => 'La région ne doit pas dépasser 100 caractères',
            
            'province.required' => 'La province est obligatoire',
            'province.string' => 'La province doit être une chaîne de caractères',
            'province.max' => 'La province ne doit pas dépasser 100 caractères',
            
            'ville.required' => 'La ville est obligatoire',
            'ville.string' => 'La ville doit être une chaîne de caractères',
            'ville.max' => 'La ville ne doit pas dépasser 100 caractères',
            
            'district_sanitaire.required' => 'Le district sanitaire est obligatoire',
            'district_sanitaire.string' => 'Le district sanitaire doit être une chaîne de caractères',
            'district_sanitaire.max' => 'Le district sanitaire ne doit pas dépasser 100 caractères'
        ];
    }
}
