<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdentifiantUnique extends Model
{
    protected $table = 'identifiants_uniques';
    protected $primaryKey = 'id';

    protected $fillable = [
        'patient_id', 'identifiant', 'methode'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}