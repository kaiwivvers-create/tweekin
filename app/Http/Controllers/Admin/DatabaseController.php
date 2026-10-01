<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class DatabaseController extends Controller
{
    /**
     * Tables that the database tools are allowed to touch.
     * Anything not listed here is never exported, imported or reset.
     */
    public const TABLES = [
        'roles' => 'Roles',
        'users' => 'Users',
        'settings' => 'Settings',
        'screenings' => 'Screenings',
        'notifications' => 'Notifications',
        'activity_logs' => 'Activity log',
    ];

    /** Reset scopes a super admin can pick from. */
    public const RESET_SCOPES = [
        'screenings' => 'Delete every screening (physical, mental and other)',
        'notifications' => 'Delete every user notification',
        'activity_logs' => 'Delete the entire activity log',
        'settings' => 'Reset all brand / app settings back to defaults',
        'users' => 'Delete every user account except your own',
        'factory' => 'Factory reset — everything above, plus roles back to their defaults',
    ];

    protected function authorizeSuperAdmin(Request $request): void
    {
        abort_unless($request->user()->isSuperAdmin(), 403, 'Only super admins can use the database tools.');
    }

    public function index(Request $request)
    {
        $this->authorizeSuperAdmin($request);

        $tables = [];
        foreach (self::TABLES as $table => $label) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            $tables[$table] = [
                'label' => $label,
                'rows' => DB::table($table)->count(),
            ];
        }

        $stats = [
            'users' => $tables['users']['rows'] ?? 0,
            'screenings' => $tables['screenings']['rows'] ?? 0,
            'super_admins' => Role::where('level', '>=', Role::SUPER_ADMIN_LEVEL)->withCount('users')->get()->sum('users_count'),
            'driver' => DB::connection()->getDriverName(),
            'database' => DB::connection()->getDatabaseName(),
        ];

        $lastExport = ActivityLog::where('action', 'exported')->latest()->first();
        $lastImport = ActivityLog::where('action', 'imported')->latest()->first();
        $lastReset = ActivityLog::where('action', 'reset')->latest()->first();

        return view('admin.database', [
            'tables' => $tables,
            'stats' => $stats,
            'resetScopes' => self::RESET_SCOPES,
            'lastExport' => $lastExport,
            'lastImport' => $lastImport,
            'lastReset' => $lastReset,
        ]);
    }

    /** Download a full JSON snapshot of the whitelisted tables. */
    public function export(Request $request)
    {
        $this->authorizeSuperAdmin($request);

        $payload = [
            'meta' => [
                'format' => 'tweek-database-v1',
                'app' => config('app.name', 'Tweek'),
                'exported_at' => now()->toIso8601String(),
                'exported_by' => $request->user()->email,
                'driver' => DB::connection()->getDriverName(),
                'tables' => [],
            ],
            'data' => [],
        ];

        foreach (array_keys(self::TABLES) as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            $rows = DB::table($table)->get()->map(fn ($row) => (array) $row)->all();
            $payload['data'][$table] = $rows;
            $payload['meta']['tables'][$table] = count($rows);
        }

        ActivityLog::record('exported', 'Exported a database backup ('.count($payload['data']).' tables).', [
            'subject_type' => self::class,
            'subject_label' => 'Database backup',
            'changes' => ['tables' => ['before' => null, 'after' => json_encode($payload['meta']['tables'])]],
        ]);

        $filename = 'tweek-backup-'.now()->format('Y-m-d-His').'.json';

        return response()->streamDownload(function () use ($payload) {
            echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }, $filename, ['Content-Type' => 'application/json']);
    }

    /** Restore a previously exported snapshot. */
    public function import(Request $request)
    {
        $this->authorizeSuperAdmin($request);

        $request->validate([
            'backup' => 'required|file|max:51200',
            'mode' => 'required|in:merge,replace',
        ]);

        $contents = file_get_contents($request->file('backup')->getRealPath());
        $decoded = json_decode($contents, true);

        if (!is_array($decoded) || !isset($decoded['data']) || !is_array($decoded['data'])) {
            return back()->with('error', 'That file is not a valid database backup.');
        }

        // Only ever import tables we recognise.
        $data = array_intersect_key($decoded['data'], self::TABLES);
        $data = array_filter($data, fn ($rows) => is_array($rows));

        if (empty($data)) {
            return back()->with('error', 'The backup contains no recognised tables.');
        }

        $actorId = $request->user()->id;
        $written = 0;

        try {
            DB::transaction(function () use ($data, $request, $actorId, &$written) {
                Schema::disableForeignKeyConstraints();

                try {
                    if ($request->mode === 'replace') {
                        foreach (array_keys($data) as $table) {
                            if ($table === 'users') {
                                DB::table('users')->where('id', '!=', $actorId)->delete();
                            } else {
                                DB::table($table)->delete();
                            }
                        }
                    }

                    foreach ($data as $table => $rows) {
                        foreach ($rows as $row) {
                            if (!is_array($row) || !isset($row['id'])) {
                                continue;
                            }

                            // Never let a backup overwrite the account doing the import,
                            // otherwise we could lose our own access halfway through.
                            if ($table === 'users' && (int) $row['id'] === (int) $actorId) {
                                continue;
                            }

                            DB::table($table)->updateOrInsert(['id' => $row['id']], $row);
                            $written++;
                        }
                    }
                } finally {
                    Schema::enableForeignKeyConstraints();
                }
            });
        } catch (Throwable $e) {
            return back()->with('error', 'Import failed: '.$e->getMessage());
        }

        // Guarantee the person running the import still has super admin access.
        $this->ensureSuperAdminAccess($request);

        ActivityLog::record('imported', "Imported a database backup ({$written} rows, {$request->mode} mode).", [
            'subject_type' => self::class,
            'subject_label' => 'Database backup',
            'changes' => ['tables' => ['before' => null, 'after' => json_encode(array_map('count', $data))]],
        ]);

        return back()->with('success', "Backup imported — {$written} rows written.");
    }

    /** Delete data, scoped to what the super admin selected. */
    public function reset(Request $request)
    {
        $this->authorizeSuperAdmin($request);

        $request->validate([
            'confirm' => 'required|string',
            'scopes' => 'required|array|min:1',
            'scopes.*' => 'in:'.implode(',', array_keys(self::RESET_SCOPES)),
        ]);

        if (strtoupper(trim($request->confirm)) !== 'RESET') {
            return back()->with('error', 'Type RESET in the confirmation box to continue.');
        }

        $scopes = $request->scopes;
        if (in_array('factory', $scopes, true)) {
            $scopes = array_merge($scopes, ['screenings', 'notifications', 'activity_logs', 'settings', 'users']);
        }

        $actorId = $request->user()->id;
        $removed = [];

        DB::transaction(function () use ($scopes, $actorId, &$removed) {
            Schema::disableForeignKeyConstraints();

            try {
                foreach (array_unique($scopes) as $scope) {
                    switch ($scope) {
                        case 'screenings':
                            $removed['screenings'] = DB::table('screenings')->delete();
                            break;
                        case 'notifications':
                            $removed['notifications'] = DB::table('notifications')->delete();
                            break;
                        case 'activity_logs':
                            $removed['activity_logs'] = DB::table('activity_logs')->delete();
                            break;
                        case 'settings':
                            $removed['settings'] = DB::table('settings')->delete();
                            break;
                        case 'users':
                            $removed['users'] = DB::table('users')->where('id', '!=', $actorId)->delete();
                            break;
                    }
                }

                if (in_array('factory', $scopes, true)) {
                    $this->restoreDefaultRoles($actorId);
                }
            } finally {
                Schema::enableForeignKeyConstraints();
            }
        });

        $this->ensureSuperAdminAccess($request);

        $summary = collect($removed)
            ->filter(fn ($count) => $count > 0)
            ->map(fn ($count, $table) => "{$count} ".str_replace('_', ' ', $table))
            ->values()
            ->implode(', ');

        ActivityLog::record('reset', 'Ran a database reset. '.($summary ? "Removed {$summary}." : 'Nothing needed removing.'), [
            'subject_type' => self::class,
            'subject_label' => 'Database reset',
            'changes' => ['scopes' => ['before' => null, 'after' => json_encode(array_values(array_unique($scopes)))]],
        ]);

        return back()->with('success', 'Reset complete. '.($summary ? "Removed {$summary}." : 'Nothing needed removing.'));
    }

    /** Make sure the acting account still holds the super admin role. */
    protected function ensureSuperAdminAccess(Request $request): void
    {
        $actor = $request->user()->fresh();

        if (!$actor) {
            return;
        }

        $superRole = Role::where('name', 'super_admin')->first()
            ?? Role::create([
                'name' => 'super_admin',
                'label' => 'Super Admin',
                'permissions' => ['*'],
                'level' => Role::SUPER_ADMIN_LEVEL,
            ]);

        if (!$actor->role_id || !$actor->role?->isSuperAdmin()) {
            DB::table('users')->where('id', $actor->id)->update([
                'role_id' => $superRole->id,
                'is_admin' => true,
                'updated_at' => now(),
            ]);
        }
    }

    /** Put the built-in roles back the way they were at install time. */
    protected function restoreDefaultRoles(int $actorId): void
    {
        $defaults = [
            ['name' => 'super_admin', 'label' => 'Super Admin', 'level' => 100, 'permissions' => ['*']],
            ['name' => 'admin', 'label' => 'Admin', 'level' => 50, 'permissions' => ['users.view', 'users.edit', 'screenings.view', 'settings.view', 'settings.edit']],
            ['name' => 'user', 'label' => 'User', 'level' => 1, 'permissions' => ['screenings.create', 'screenings.view_own']],
        ];

        foreach ($defaults as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                ['label' => $role['label'], 'level' => $role['level'], 'permissions' => $role['permissions']]
            );
        }

        $superRole = Role::where('name', 'super_admin')->first();

        if (!$superRole) {
            return;
        }

        // Keep every role_id valid, then make sure the acting account is a super admin.
        $validIds = Role::pluck('id')->all();
        DB::table('users')->whereNotIn('role_id', $validIds)->update(['role_id' => null]);
        DB::table('users')->where('id', $actorId)->update([
            'role_id' => $superRole->id,
            'is_admin' => true,
        ]);
        DB::table('users')->where('role_id', $superRole->id)->update(['is_admin' => true]);
    }
}
