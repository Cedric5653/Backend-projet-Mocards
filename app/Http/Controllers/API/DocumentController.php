<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DocumentMedical;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Upload a new document
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // max 10MB
            'carnet_id' => 'required|exists:carnet_numerique,carnet_id',
            'type_document' => 'required|in:radiographie,analyse_sang,ordonnance,compte_rendu'
        ]);

        try {
            $file = $request->file('file');
            $path = $file->store('documents', 'private'); // stockage privé

            $document = DocumentMedical::create([
                'carnet_id' => $request->carnet_id,
                'type_document' => $request->type_document,
                'fichier_url' => $path,
                'date_creation' => now(),
                'created_by' => auth()->id()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Document uploaded successfully',
                'data' => $document
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload document',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download a document
     */
    public function download($id)
    {
        $document = DocumentMedical::findOrFail($id);

        // Vérifier les permissions
        if (!auth()->user()->can('view', $document)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        if (Storage::disk('private')->exists($document->fichier_url)) {
            return Storage::disk('private')->download(
                $document->fichier_url,
                $document->type_document . '_' . $document->created_at->format('Y-m-d') . '.' . pathinfo($document->fichier_url, PATHINFO_EXTENSION)
            );
        }

        return response()->json([
            'status' => 'error',
            'message' => 'File not found'
        ], 404);
    }

    /**
     * Get documents list
     */
    public function index(Request $request)
    {
        $documents = DocumentMedical::query()
            ->when($request->carnet_id, function($query, $carnetId) {
                return $query->where('carnet_id', $carnetId);
            })
            ->when($request->type_document, function($query, $type) {
                return $query->where('type_document', $type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $documents
        ]);
    }
}