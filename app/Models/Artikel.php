<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Artikel extends Model
{
    protected $table = 'artikel';

    protected $fillable = [
        'admin_id',
        'judul',
        'slug',
        'kategori_artikel_id',
        'konten',
        'thumbnail',
        'is_pinned',
        'status',
        'views_count',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    // Relasi Many-to-One: Artikel milik 1 Kategori
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriArtikel::class, 'kategori_artikel_id');
    }
}
