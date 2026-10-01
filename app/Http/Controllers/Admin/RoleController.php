<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Throwable;

class RoleController extends Controller
{
    protected function authorizeManage(Request $request): void
    {
        $user = $request->user();
        abort_unless($user->isSuperAdmin() || $user->hasPermission('roles.manage'), 403);
    }

    /** Super admins can touch super admin roles; everyone else cannot. */
    protected function assertCanTouchRole(Request $request, ?Role $role, int $newLevel = 0): void
    {
        if ($request->user()->isSuperAdmin()) {
            return;
        }

        if ($role && $role->isSuperAdmin()) {
            abort(403, 'Only super admins can modify super admin roles.');
        }

        if ($newLevel >= Role::SUPER_ADMIN_LEVEL) {
            abort(403, 'Only super admins can create or assign super admin roles.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeManage($request);

        $isSuperAdmin = $request->user()->isSuperAdmin();

        $roles = Role::withCount('users')
            ->when(!$isSuperAdmin, fn ($q) => $q->where('level', '<', Role::SUPER_ADMIN_LEVEL))
            ->orderByDesc('level')
            ->orderBy('name')
            ->get();

        return view('admin.permissions', [
            'roles' => $roles,
            'catalog' => Role::CATALOG,
            'allPermissions' => Role::allPermissions(),
            'isSuperAdmin' => $isSuperAdmin,
            'superAdminLevel' => Role::SUPER_ADMIN_LEVEL,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeManage($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('roles', 'name')],
            'label' => 'required|string|max:255',
            'level' => 'required|integer|min:0|max:100',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $this->assertCanTouchRole($request, null, (int) $validated['level']);

        $role = Role::create([
            'name' => strtolower($validated['name']),
            'label' => $validated['label'],
            'level' => (int) $validated['level'],
            'permissions' => $this->cleanPermissions($validated['permissions'] ?? []),
        ]);

        ActivityLog::record('created', "Created the role \"{$role->label}\".", [
            'subject_type' => Role::class,
            'subject_id' => $role->id,
            'subject_label' => $role->label,
            'changes' => ['role' => ['before' => null, 'after' => $role->name]],
        ]);

        return back()->with('success', "Role \"{$role->label}\" created.");
    }

    public function update(Request $request, Role $role)
    {
        $this->authorizeManage($request);
        $this->assertCanTouchRole($request, $role);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', 'alpha_dash', Rule::unique('roles', 'name')->ignore($role->id)],
            'label' => 'required|string|max:255',
            'level' => 'required|integer|min:0|max:100',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $this->assertCanTouchRole($request, $role, (int) $validated['level']);

        // Never let a role that is in use lose its super admin status by accident.
        if ($role->isSuperAdmin() && (int) $validated['level'] < Role::SUPER_ADMIN_LEVEL && !$request->user()->isSuperAdmin()) {
            abort(403, 'Only super admins can demote a super admin role.');
        }

        $before = $role->only(['name', 'label', 'level', 'permissions']);

        $role->label = $validated['label'];
        $role->level = (int) $validated['level'];
        $role->permissions = $this->cleanPermissions($validated['permissions'] ?? []);

        if (isset($validated['name'])) {
            $role->name = strtolower($validated['name']);
        }

        try {
            $role->save();
        } catch (Throwable $e) {
            return back()->with('error', 'Could not save role: '.$e->getMessage());
        }

        ActivityLog::record('updated', "Updated the role \"{$role->label}\".", [
            'subject_type' => Role::class,
            'subject_id' => $role->id,
            'subject_label' => $role->label,
            'changes' => ActivityLog::diff(
                array_merge($before, ['permissions' => json_encode($before['permissions'])]),
                $role->only(['name', 'label', 'level', 'permissions'])
            ),
        ]);

        return back()->with('success', "Role \"{$role->label}\" updated.");
    }

    public function destroy(Request $request, Role $role)
    {
        $this->authorizeManage($request);
        $this->assertCanTouchRole($request, $role);

        if ($role->users()->exists()) {
            return back()->with('error', "Cannot delete \"{$role->label}\" — {$role->users()->count()} user(s) still have it. Reassign them first.");
        }

        if ($role->isSuperAdmin()) {
            return back()->with('error', 'The super admin role cannot be deleted.');
        }

        $label = $role->label;
        $role->delete();

        ActivityLog::record('deleted', "Deleted the role \"{$label}\".", [
            'subject_type' => Role::class,
            'subject_id' => $role->id,
            'subject_label' => $label,
        ]);

        return back()->with('success', "Role \"{$label}\" deleted.");
    }

    /** Keep only permission keys the app actually knows, plus the wildcard. */
    protected function cleanPermissions(array $permissions): array
    {
        if (in_array('*', $permissions, true)) {
            return ['*'];
        }

        $known = array_keys(Role::allPermissions());

        return array_values(array_intersect($permissions, $known));
    }
}
