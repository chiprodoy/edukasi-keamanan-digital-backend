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
        'judul',
        'deskripsi',
        'durasi_menit',
        'passing_score',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'durasi_menit' => 'integer',
        'passing_score' => 'integer',
    ];

    public function materi(): BelongsTo
    {
        return $this->belongsTo(Materi::class);
    }

    public function soal_kuis(): HasMany
    {
        return $this->hasMany(SoalKuis::class);
    }

    public function opsiJawaban(): HasMany
    {
        return $this->hasMany(OpsiJawaban::class);
    }
}
