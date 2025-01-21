<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentMedical extends Model
{
    protected $table = 'documents_medicaux';
    protected $primaryKey = 'document_id';
    public $timestamps = false;

    protected $fillable = [
        'carnet_id', 'type_document', 'fichier_url',
        'date_creation', 'created_by'
    ];

    protected $casts = [
        'date_creation' => 'date'
    ];

    public function carnetNumerique()
    {
        return $this->belongsTo(CarnetNumerique::class, 'carnet_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function examenLaboratoire()
    {
        return $this->hasOne(ExamenLaboratoire::class, 'document_id');
    }
}