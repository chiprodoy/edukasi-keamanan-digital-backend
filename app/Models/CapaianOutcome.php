<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CapaianOutcome extends Model
{
    protected $table = 'capaian_outcome';

    protected $fillable = [
        'warga_id',
        'outcome_id',
        'skor_tertinggi',
    ];

    protected $appends = ['deskripsi_dinamis'];

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class);
    }

    public function outcome(): BelongsTo
    {
        return $this->belongsTo(Outcome::class);
    }

    /**
     * Eloquent Accessor: Mengambil deskripsi narasi rubrik secara dinamis
     * berdasarkan range batas skor.
     */
    public function getDeskripsiDinamisAttribute()
    {
        if (!$this->outcome_id || is_null($this->skor_tertinggi)) {
            return 'Belum ada deskripsi rubrik untuk skor ini.';
        }

        return RubrikPenilaian::where('outcome_id', $this->outcome_id)
            ->where('batas_bawah_skor', '<=', $this->skor_tertinggi)
            ->where('batas_atas_skor', '>=', $this->skor_tertinggi)
            ->value('deskripsi_capaian') ?? 'Belum ada deskripsi rubrik untuk skor ini.';
    }
}
