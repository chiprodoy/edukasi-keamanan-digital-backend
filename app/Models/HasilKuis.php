<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HasilKuis extends Model
{
    protected $table = 'hasil_kuis';

    protected $fillable = [
        'warga_id',
        'materi_id',
        'total_skor',
        'waktu_selesai',
    ];

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class);
    }

    public function materi(): BelongsTo
    {
        return $this->belongsTo(Materi::class);
    }

    public function detailHasil(): HasMany
    {
        return $this->hasMany(DetailHasilKuis::class);
    }
}
