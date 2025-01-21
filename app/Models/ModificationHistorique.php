<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModificationHistorique extends Model
{
    protected $table = 'modifications_historique';
    protected $primaryKey = 'historique_id';
    public $timestamps = false;

    protected $fillable = [
        'table_name', 'record_id', 'field_modified',
        'old_value', 'new_value', 'modified_by', 'modified_at'
    ];

    protected $casts = [
        'modified_at' => 'datetime'
    ];

    public function modifiedBy()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }
}