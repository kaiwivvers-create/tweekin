<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Screening extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'type',
        'title',
        'data',
        'severity',
        'assessment',
    ];

    protected $casts = [
        'data' => 'array',
        'severity' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get screenings for a user or guest session
     */
    public static function forCurrentUser($sessionId = null)
    {
        if (auth()->check()) {
            return static::where('user_id', auth()->id())->latest();
        }
        if ($sessionId) {
            return static::where('session_id', $sessionId)->latest();
        }
        return static::whereRaw('1 = 0'); // empty query
    }
}
