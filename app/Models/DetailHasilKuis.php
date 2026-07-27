<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailHasilKuis extends Model
{
    protected $table = 'detail_hasil_kuis';

    protected $fillable = [
        'hasil_kuis_id',
        'kuis_id',
        'opsi_dipilih_id',
        'is_benar',
        'poin_didapat',
    ];

    protected $casts = [
        'is_benar' => 'boolean',
    ];

    public function hasilKuis(): BelongsTo
    {
        return $this->belongsTo(HasilKuis::class);
    }

    public function kuis(): BelongsTo
    {
        return $this->belongsTo(Kuis::class);
    }

    public function opsiDipilih(): BelongsTo
    {
        return $this->belongsTo(OpsiJawaban::class, 'opsi_dipilih_id');
    }
}
