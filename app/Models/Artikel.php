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
        'kategori',
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
}
