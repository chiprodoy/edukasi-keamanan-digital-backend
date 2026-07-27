<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kuis extends Model
{
    protected $table = 'kuis';

    protected $fillable = [
        'materi_id',
        'teks_soal',
        'poin',
    ];

    public function materi(): BelongsTo
    {
        return $this->belongsTo(Materi::class);
    }

    public function opsiJawaban(): HasMany
    {
        return $this->hasMany(OpsiJawaban::class);
    }
}
