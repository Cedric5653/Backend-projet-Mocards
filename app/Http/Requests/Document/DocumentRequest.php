<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;


class DocumentRequest extends FormRequest
{
    public function authorize():bool
    {
        return true;
    }

    public function rules():array
    {
        return [
            'carnet_id' => 'required|exists:carnet_numerique,carnet_id',
            'type_document' => 'required|in:radiographie,analyse_sang,ordonnance,compte_rendu',
            'fichier' => 'required|file|max:10240', // 10MB max
            'date_creation' => 'required|date'
        ];
    }
}