<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'is_passed',
    ];

    protected $casts = [
        'score' => 'integer',
        'is_passed' => 'boolean',
    ];

    /**
     * Relasi ke Warga / User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Kuis
     */
    public function kuis(): BelongsTo
    {
        return $this->belongsTo(Kuis::class, 'quiz_id');
    }
}
