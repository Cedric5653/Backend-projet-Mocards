<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localisation extends Model
{
    protected $table = 'localisation';
    protected $primaryKey = 'localisation_id';
    public $timestamps = false;

    protected $fillable = [
        'region', 'province', 'ville', 'district_sanitaire'
    ];

    public function patients()
    {
        return $this->hasMany(Patient::class, 'localisation_id');
    }
}