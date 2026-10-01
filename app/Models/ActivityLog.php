<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'subject_type',
        'subject_id',
        'subject_label',
        'description',
        'changes',
        'ip_address',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    /** Friendly labels for each action type. */
    public const ACTIONS = [
        'created' => 'Created',
        'updated' => 'Updated',
        'deleted' => 'Deleted',
        'exported' => 'Exported',
        'imported' => 'Imported',
        'reset' => 'Reset',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Persist an activity entry. Safe to call when nobody is authenticated.
     */
    public static function record(string $action, string $description, array $options = []): self
    {
        $actor = Auth::user();

        return static::create([
            'user_id' => $actor?->id,
            'user_name' => $actor?->name ?? 'System',
            'user_role' => $actor?->role?->label,
            'action' => $action,
            'subject_type' => $options['subject_type'] ?? null,
            'subject_id' => ($options['subject_id'] ?? null) !== null ? (string) $options['subject_id'] : null,
            'subject_label' => $options['subject_label'] ?? null,
            'description' => $description,
            'changes' => $options['changes'] ?? null,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Build a before/after diff, ignoring fields that did not actually change.
     *
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     * @return array<string, array{before: mixed, after: mixed}>
     */
    public static function diff(array $before, array $after): array
    {
        $changes = [];

        foreach ($after as $key => $new) {
            $old = $before[$key] ?? null;
            if ($old == $new) {
                continue;
            }
            $changes[$key] = [
                'before' => self::stringify($old),
                'after' => self::stringify($new),
            ];
        }

        return $changes;
    }

    /** Short readable rendering of a value for the diff view. */
    protected static function stringify(mixed $value): ?string
    {
        if (is_array($value)) {
            return json_encode($value);
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if ($value === null) {
            return null;
        }

        $string = (string) $value;

        return mb_strlen($string) > 300 ? mb_substr($string, 0, 300).'…' : $string;
    }

    /** Human name for the logged subject type. */
    public function getSubjectNameAttribute(): ?string
    {
        return $this->subject_type ? class_basename($this->subject_type) : null;
    }

    public function getActionLabelAttribute(): string
    {
        return self::ACTIONS[$this->action] ?? ucfirst($this->action);
    }

    /** Colour tokens used by the activity feed badges. */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'created' => 'physical',
            'updated' => 'mental',
            'deleted' => 'danger',
            'exported' => 'other',
            'imported' => 'mental',
            'reset' => 'danger',
            default => 'warm',
        };
    }
}
