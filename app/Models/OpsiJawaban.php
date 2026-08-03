<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpsiJawaban extends Model
{
    protected $table = 'opsi_jawaban';

    protected $fillable = [
        'soal_kuis_id',
        'teks_jawaban',
        'poin',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    // public function kuis(): BelongsTo
    // {
    //     return $this->belongsTo(Kuis::class);
    // }

    public function soalKuis(): BelongsTo
    {
        return $this->belongsTo(SoalKuis::class, 'soal_kuis_id');
    }
}
