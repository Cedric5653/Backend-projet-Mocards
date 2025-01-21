<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CarteUrgence;
use App\Http\Requests\API\Dossier\CarteUrgenceRequest;
use Illuminate\Http\Request;

class CarteUrgenceController extends Controller
{
    public function show($patientId)
    {
        $carteUrgence = CarteUrgence::where('patient_id', $patientId)
            ->with('patient')
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $carteUrgence
        ]);
    }

    public function store(CarteUrgenceRequest $request)
    {
        $carteUrgence = CarteUrgence::create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Carte d\'urgence créée avec succès',
            'data' => $carteUrgence
        ], 201);
    }

    public function update(CarteUrgenceRequest $request, $id)
    {
        $carteUrgence = CarteUrgence::findOrFail($id);
        $carteUrgence->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Carte d\'urgence mise à jour avec succès',
            'data' => $carteUrgence
        ]);
    }
}