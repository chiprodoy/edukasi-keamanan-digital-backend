<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Outcome extends Model
{
    protected $fillable = [
        'kode_outcome',
        'judul_kompetensi',
        'deskripsi',
    ];

    public function materi(): BelongsToMany
    {
        return $this->belongsToMany(Materi::class, 'materi_outcome');
    }

    public function rubrikPenilaian(): HasMany
    {
        return $this->hasMany(RubrikPenilaian::class);
    }

    public function capaianWarga(): HasMany
    {
        return $this->hasMany(CapaianOutcome::class);
    }
}
