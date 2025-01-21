<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AntecedentMedical extends Model
{
    protected $table = 'antecedents_medicaux';
    protected $primaryKey = 'antecedent_id';
    public $timestamps = false;

    protected $fillable = [
        'patient_id', 'type_antecedent', 'description',
        'date_evenement'
    ];

    protected $casts = [
        'date_evenement' => 'date'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}