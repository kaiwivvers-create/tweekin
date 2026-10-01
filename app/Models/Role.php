<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'label',
        'permissions',
        'level',
    ];

    protected $casts = [
        'permissions' => 'array',
        'level' => 'integer',
    ];

    /** Level at which an account is treated as a super admin. */
    public const SUPER_ADMIN_LEVEL = 100;

    /**
     * Every permission the app knows about, grouped for the admin UI.
     * Super admins bypass this list entirely.
     */
    public const CATALOG = [
        'Users' => [
            'users.view' => 'View users',
            'users.edit' => 'Edit users',
            'users.delete' => 'Delete users',
            'users.impersonate' => 'Sign in as another user',
        ],
        'Screenings' => [
            'screenings.view' => 'View all screenings',
            'screenings.view_own' => 'View own screenings',
            'screenings.create' => 'Create screenings',
            'screenings.edit' => 'Edit screenings',
            'screenings.delete' => 'Delete screenings',
        ],
        'Reports' => [
            'reports.view' => 'View reports',
            'activity.view' => 'View the activity log',
        ],
        'Configuration' => [
            'settings.view' => 'View settings',
            'settings.edit' => 'Edit settings',
            'roles.manage' => 'Manage roles',
        ],
        'System' => [
            'database.manage' => 'Export, import and reset the database',
            'data.manage' => 'Edit raw records',
        ],
    ];

    /** Flat permission map: key => label. */
    public static function allPermissions(): array
    {
        $flat = [];
        foreach (static::CATALOG as $group) {
            foreach ($group as $key => $label) {
                $flat[$key] = $label;
            }
        }

        return $flat;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function hasPermission(string $permission): bool
    {
        if (!$this->permissions) {
            return false;
        }

        return in_array($permission, $this->permissions) || in_array('*', $this->permissions);
    }

    public function isSuperAdmin(): bool
    {
        return $this->level >= self::SUPER_ADMIN_LEVEL || $this->name === 'super_admin';
    }

    public function hasAllPermissions(): bool
    {
        return in_array('*', $this->permissions ?? []);
    }
}
