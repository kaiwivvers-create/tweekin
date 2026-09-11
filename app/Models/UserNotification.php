<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'icon',
        'read',
        'action_url',
        'action_label',
    ];

    protected $casts = [
        'read' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as read
     */
    public function markRead(): void
    {
        $this->update(['read' => true]);
    }

    /**
     * Get unread count for a user
     */
    public static function unreadCount(int $userId): int
    {
        return static::where('user_id', $userId)->where('read', false)->count();
    }

    /**
     * Create a screening completion notification
     */
    public static function forScreeningComplete(Screening $screening): self
    {
        $typeLabel = ucfirst($screening->type);
        $severityText = match(true) {
            $screening->severity <= 2 => 'mild — keep an eye on it',
            $screening->severity <= 3 => 'moderate — worth paying attention to',
            default => 'significant — consider reaching out to a professional',
        };

        return static::create([
            'user_id' => $screening->user_id,
            'title' => "{$typeLabel} screening complete",
            'message' => "Your " . strtolower($typeLabel) . " screening ({$screening->title}) showed a severity of {$screening->severity}/5. That's {$severityText}.",
            'type' => $screening->severity >= 4 ? 'warning' : 'success',
            'icon' => $screening->type === 'physical' ? 'heart' : ($screening->type === 'mental' ? 'brain' : 'question'),
            'action_url' => route('screenings.show', $screening),
            'action_label' => 'View results',
        ]);
    }

    /**
     * Create a reminder notification
     */
    public static function createReminder(int $userId, string $title, string $message, ?string $actionUrl = null): self
    {
        return static::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => 'reminder',
            'icon' => 'clock',
            'action_url' => $actionUrl,
            'action_label' => $actionUrl ? 'View' : null,
        ]);
    }
}
