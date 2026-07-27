<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materi extends Model
{
    protected $table = 'materi';

    protected $fillable = [
        'admin_id',
        'judul',
        'slug',
        'kategori',
        'konten',
        'media_url',
        'status',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function outcomes(): BelongsToMany
    {
        return $this->belongsToMany(Outcome::class, 'materi_outcome');
    }

    public function kuis(): HasMany
    {
        return $this->hasMany(Kuis::class);
    }

    public function hasilKuis(): HasMany
    {
        return $this->hasMany(HasilKuis::class);
    }
}
