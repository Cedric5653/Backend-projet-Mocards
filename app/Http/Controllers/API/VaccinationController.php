<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Vaccination;
use App\Http\Requests\API\Medical\VaccinationRequest;
use Illuminate\Http\Request;

class VaccinationController extends Controller
{
    public function index(Request $request)
    {
        $query = Vaccination::query();
        
        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        $vaccinations = $query->with('patient')
                            ->orderBy('date_vaccination', 'desc')
                            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $vaccinations
        ]);
    }

    public function store(VaccinationRequest $request)
    {
        $vaccination = Vaccination::create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Vaccination enregistrée avec succès',
            'data' => $vaccination
        ], 201);
    }

    public function show($id)
    {
        $vaccination = Vaccination::with('patient')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $vaccination
        ]);
    }

    public function update(VaccinationRequest $request, $id)
    {
        $vaccination = Vaccination::findOrFail($id);
        $vaccination->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Vaccination mise à jour avec succès',
            'data' => $vaccination
        ]);
    }

    public function destroy($id)
    {
        $vaccination = Vaccination::findOrFail($id);
        $vaccination->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Vaccination supprimée avec succès'
        ]);
    }
}