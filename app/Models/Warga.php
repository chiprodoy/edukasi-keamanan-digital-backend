<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warga extends Model
{
    use HasFactory;

    protected $table = 'warga';

    protected $fillable = [
        'user_id',
        'nik',
        'no_hp',
        'kecamatan',
        'level_literasi',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasilKuis(): HasMany
    {
        return $this->hasMany(HasilKuis::class);
    }

    public function capaianOutcome(): HasMany
    {
        return $this->hasMany(CapaianOutcome::class);
    }
}
