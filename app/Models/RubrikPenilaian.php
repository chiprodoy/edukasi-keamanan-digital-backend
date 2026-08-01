<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RubrikPenilaian extends Model
{
    protected $table = 'rubrik_penilaian';

    protected $fillable = [
        'outcome_id',
        'batas_bawah_skor',
        'batas_atas_skor',
        'label_level',
        'level',
        'deskripsi_capaian',
    ];

    public function outcome(): BelongsTo
    {
        return $this->belongsTo(Outcome::class);
    }
}
