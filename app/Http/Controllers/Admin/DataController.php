<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Throwable;

class DataController extends Controller
{
    /** Tables a super admin can browse and edit record by record. */
    public const TABLES = [
        'users' => ['label' => 'Users', 'title' => 'name'],
        'screenings' => ['label' => 'Screenings', 'title' => 'title'],
        'settings' => ['label' => 'Settings', 'title' => 'key'],
        'roles' => ['label' => 'Roles', 'title' => 'label'],
        'notifications' => ['label' => 'Notifications', 'title' => 'title'],
        'activity_logs' => ['label' => 'Activity log', 'title' => 'description'],
    ];

    /** Columns that hold JSON and need validation before saving. */
    public const JSON_COLUMNS = [
        'screenings' => ['data'],
        'roles' => ['permissions'],
        'activity_logs' => ['changes'],
    ];

    /** Columns that must never be edited from the UI. */
    protected const READ_ONLY = ['id', 'created_at', 'updated_at'];

    protected function authorizeSuperAdmin(Request $request): void
    {
        abort_unless($request->user()->isSuperAdmin(), 403, 'Only super admins can edit raw records.');
    }

    protected function resolveTable(string $table): string
    {
        abort_unless(array_key_exists($table, self::TABLES) && Schema::hasTable($table), 404);
        return $table;
    }

    /**
     * Everything a record editor needs for a table.
     *
     * Shared by the full edit page and the inline modals on the admin lists so
     * both always agree on which columns exist and which roles are assignable.
     *
     * @return array{meta:array, columns:array, jsonColumns:array, roles:\Illuminate\Support\Collection}
     */
    public static function editorData(string $table): array
    {
        $controller = new static();

        return [
            'meta' => self::TABLES[$table],
            'columns' => $controller->columns($table),
            'jsonColumns' => self::JSON_COLUMNS[$table] ?? [],
            'roles' => $table === 'users' ? Role::orderBy('level', 'desc')->get() : collect(),
        ];
    }

    /** Column metadata for a table, with the noisy bits filtered out. */
    protected function columns(string $table): array
    {
        return collect(Schema::getColumns($table))
            ->reject(fn ($column) => in_array($column['name'], ['remember_token'], true))
            ->map(fn ($column) => [
                'name' => $column['name'],
                'type' => $column['type_name'] ?? $column['type'],
                'nullable' => (bool) $column['nullable'],
                'default' => $column['default'],
                'auto' => (bool) ($column['auto_increment'] ?? false),
            ])
            ->values()
            ->all();
    }

    public function index(Request $request)
    {
        $this->authorizeSuperAdmin($request);

        $tables = [];
        foreach (self::TABLES as $table => $meta) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            $tables[$table] = $meta + ['rows' => DB::table($table)->count()];
        }

        $recent = ActivityLog::where('subject_type', DataController::class)
            ->orWhereIn('action', ['updated', 'deleted', 'created'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.data.index', [
            'tables' => $tables,
            'recent' => $recent,
            'queryResult' => session('queryResult'),
            'queryError' => session('queryError'),
            'querySql' => session('querySql'),
        ]);
    }

    public function browse(Request $request, string $table)
    {
        $this->authorizeSuperAdmin($request);
        $table = $this->resolveTable($table);

        $meta = self::TABLES[$table];
        $columns = $this->columns($table);

        $query = DB::table($table);

        if ($request->filled('q')) {
            $term = $request->q;
            $searchable = collect($columns)
                ->filter(fn ($c) => !$c['auto'] && in_array($c['name'], ['name', 'email', 'title', 'key', 'label', 'description', 'type', 'message'], true))
                ->pluck('name')
                ->all();

            if (empty($searchable)) {
                $searchable = ['id'];
            }

            $query->where(function ($w) use ($searchable, $term) {
                foreach ($searchable as $column) {
                    $w->orWhere($column, 'like', "%{$term}%");
                }
            });
        }

        $sort = $request->get('sort', 'id');
        $direction = $request->get('direction') === 'asc' ? 'asc' : 'desc';
        $validSort = in_array($sort, collect($columns)->pluck('name')->all(), true) ? $sort : 'id';

        $records = $query->orderBy($validSort, $direction)->paginate(20)->withQueryString();

        return view('admin.data.browse', self::editorData($table) + [
            'table' => $table,
            'records' => $records,
            'titleColumn' => in_array($meta['title'], collect($columns)->pluck('name')->all(), true) ? $meta['title'] : null,
            'sort' => $validSort,
            'direction' => $direction,
        ]);
    }

    public function show(Request $request, string $table, string $id)
    {
        $this->authorizeSuperAdmin($request);
        $table = $this->resolveTable($table);

        $record = DB::table($table)->where('id', $id)->first();
        abort_if(!$record, 404, 'Record not found.');

        return view('admin.data.edit', self::editorData($table) + [
            'table' => $table,
            'record' => $record,
        ]);
    }

    public function update(Request $request, string $table, string $id)
    {
        $this->authorizeSuperAdmin($request);
        $table = $this->resolveTable($table);

        $record = DB::table($table)->where('id', $id)->first();
        abort_if(!$record, 404, 'Record not found.');

        $editable = collect($this->columns($table))
            ->reject(fn ($c) => $c['auto'] || in_array($c['name'], self::READ_ONLY, true))
            ->keyBy('name');

        $jsonColumns = self::JSON_COLUMNS[$table] ?? [];
        $errors = [];
        $update = [];

        foreach ($editable as $name => $column) {
            if (!$request->has($name)) {
                continue;
            }

            $value = $request->input($name);

            // Passwords: blank means "leave it alone", anything else gets hashed.
            if ($table === 'users' && $name === 'password') {
                if ($value === null || $value === '') {
                    continue;
                }
                $update['password'] = Hash::make($value);
                continue;
            }

            if (in_array($name, $jsonColumns, true) || ($table === 'settings' && $name === 'value' && $request->input('type') === 'json')) {
                if ($value === null || $value === '') {
                    $update[$name] = null;
                    continue;
                }
                json_decode($value);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $errors[] = "{$name} is not valid JSON.";
                    continue;
                }
                $update[$name] = $value;
                continue;
            }

            if ($value === '') {
                $update[$name] = $column['nullable'] ? null : '';
                continue;
            }

            $update[$name] = $this->cast($column['type'], $value, $name, $errors);
        }

        if ($table === 'users' && isset($update['role_id']) && $update['role_id'] !== null) {
            if (!Role::where('id', $update['role_id'])->exists()) {
                $errors[] = 'That role does not exist.';
            }
        }

        if ($errors) {
            return back()->withInput()->with('error', implode(' ', $errors));
        }

        try {
            $update['updated_at'] = now();
            DB::table($table)->where('id', $id)->update($update);
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Could not save: '.$e->getMessage());
        }

        ActivityLog::record('updated', "Edited ".self::TABLES[$table]['label'].' record #'.$id.'.', [
            'subject_type' => $table,
            'subject_id' => $id,
            'subject_label' => $record->{self::TABLES[$table]['title']} ?? null,
            'changes' => ActivityLog::diff((array) $record, $update),
        ]);

        // Saving from a modal should drop you back on the list you opened it from,
        // not push you onto the standalone edit page. Only same-site paths count.
        $return = (string) $request->input('_return', '');
        $redirect = (str_starts_with($return, '/') && !str_starts_with($return, '//'))
            ? redirect()->to($return)
            : redirect()->route('admin.data.show', [$table, $id]);

        return $redirect->with('success', 'Record saved.');
    }

    public function destroy(Request $request, string $table, string $id)
    {
        $this->authorizeSuperAdmin($request);
        $table = $this->resolveTable($table);

        $record = DB::table($table)->where('id', $id)->first();
        abort_if(!$record, 404, 'Record not found.');

        // Don't let a super admin delete the account they're signed in with.
        if ($table === 'users' && (int) $id === (int) $request->user()->id) {
            return back()->with('error', 'You cannot delete the account you are signed in with.');
        }

        DB::table($table)->where('id', $id)->delete();

        ActivityLog::record('deleted', 'Deleted '.self::TABLES[$table]['label'].' record #'.$id.'.', [
            'subject_type' => $table,
            'subject_id' => $id,
            'subject_label' => $record->{self::TABLES[$table]['title']} ?? null,
            'changes' => collect((array) $record)
                ->mapWithKeys(fn ($v, $k) => [$k => ['before' => is_scalar($v) || $v === null ? $v : json_encode($v), 'after' => null]])
                ->all(),
        ]);

        return redirect()->route('admin.data.browse', $table)->with('success', 'Record deleted.');
    }

    /** Free-form SQL console — reads run as-is, writes need an explicit confirmation. */
    public function query(Request $request)
    {
        $this->authorizeSuperAdmin($request);

        $request->validate(['sql' => 'required|string|max:20000']);

        $sql = trim($request->sql);
        $statement = preg_replace('/\s+/', ' ', $sql);
        $isRead = (bool) preg_match('/^(select|show|describe|desc|explain|pragma|with)\b/i', $statement);

        if (!$isRead && strtoupper(trim($request->input('confirm', ''))) !== 'RUN') {
            return back()->with('queryError', 'That is a write statement. Type RUN in the confirmation box to execute it.')
                ->with('querySql', $sql);
        }

        try {
            if ($isRead) {
                $rows = DB::select($sql);
                $result = [
                    'kind' => 'rows',
                    'columns' => $rows ? array_keys((array) $rows[0]) : [],
                    'rows' => array_map(fn ($row) => (array) $row, $rows),
                    'count' => count($rows),
                ];
            } else {
                $affected = DB::statement($sql);
                $result = [
                    'kind' => 'affected',
                    'affected' => is_int($affected) ? $affected : 0,
                ];

                ActivityLog::record('updated', 'Ran a raw SQL statement.', [
                    'subject_type' => self::class,
                    'subject_label' => 'SQL console',
                    'changes' => ['sql' => ['before' => null, 'after' => $sql]],
                ]);
            }
        } catch (Throwable $e) {
            return back()->with('queryError', $e->getMessage())->with('querySql', $sql);
        }

        return back()->with('queryResult', $result)->with('querySql', $sql);
    }

    /** Convert the raw form value to something the column can store. */
    protected function cast(string $type, mixed $value, string $name, array &$errors): mixed
    {
        $type = strtolower($type);

        if (str_contains($type, 'int')) {
            if (!is_numeric($value)) {
                $errors[] = "{$name} must be a number.";
                return $value;
            }
            return (int) $value;
        }

        if (str_contains($type, 'bool')) {
            return (bool) $value;
        }

        if (str_contains($type, 'float') || str_contains($type, 'double') || str_contains($type, 'decimal') || str_contains($type, 'real')) {
            if (!is_numeric($value)) {
                $errors[] = "{$name} must be a number.";
                return $value;
            }
            return (float) $value;
        }

        if (str_contains($type, 'datetime') || str_contains($type, 'timestamp') || str_contains($type, 'date')) {
            try {
                return \Carbon\Carbon::parse($value)->toDateTimeString();
            } catch (Throwable) {
                $errors[] = "{$name} is not a valid date.";
                return $value;
            }
        }

        return $value;
    }
}
