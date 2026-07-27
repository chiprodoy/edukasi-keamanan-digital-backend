<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpsiJawaban extends Model
{
    protected $table = 'opsi_jawaban';

    protected $fillable = [
        'kuis_id',
        'teks_pilihan',
        'is_benar',
    ];

    protected $casts = [
        'is_benar' => 'boolean',
    ];

    public function kuis(): BelongsTo
    {
        return $this->belongsTo(Kuis::class);
    }
}
