<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriArtikel extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kategori',
        'slug',
        'deskripsi',
    ];

    // Relasi One-to-Many: 1 Kategori memiliki Banyak Artikel
    public function artikels(): HasMany
    {
        return $this->hasMany(Artikel::class, 'kategori_artikel_id');
    }
}
