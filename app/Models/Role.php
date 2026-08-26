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
}
