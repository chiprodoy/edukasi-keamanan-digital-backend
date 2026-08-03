<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoalKuis extends Model
{
    use HasFactory;

    protected $table = 'soal_kuis';

    protected $fillable = [
        'kuis_id',
        'teks_soal',
        'poin',
    ];

    protected $casts = [
        'kuis_id' => 'integer',
        'poin'    => 'integer',
    ];

    /**
     * Relasi Kebalikan ke Header Kuis
     */
    public function kuis()
    {
        return $this->belongsTo(Kuis::class, 'kuis_id');
    }

    /**
     * Relasi ke Opsi Jawaban
     */
    public function opsiJawaban()
    {
        return $this->hasMany(OpsiJawaban::class, 'soal_kuis_id');
    }
}
