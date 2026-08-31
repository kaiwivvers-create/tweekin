<?php

use App\Models\User;
use App\Models\Screening;
use App\Models\Role;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', function () {
        $isSuperAdmin = auth()->user()->role->level >= 100;
        $query = User::with('role');
        if (!$isSuperAdmin) {
            $query->whereHas('role', fn($q) => $q->where('level', '<', 100));
        }
        $stats = [
            'total_users' => $query->count(),
            'total_screenings' => Screening::count(),
            'physical_screenings' => Screening::where('type', 'physical')->count(),
            'mental_screenings' => Screening::where('type', 'mental')->count(),
            'other_screenings' => Screening::where('type', 'other')->count(),
            'recent_users' => (clone $query)->latest()->take(5)->get(),
            'recent_screenings' => Screening::latest()->take(5)->get(),
        ];
        return view('admin.dashboard', $stats);
    })->name('dashboard');

    // Users
    Route::get('/users', function () {
        $isSuperAdmin = auth()->user()->role->level >= 100;
        $users = User::with('role');
        if (!$isSuperAdmin) {
            $users->whereHas('role', fn($q) => $q->where('level', '<', 100));
        }
        $users = $users->latest()->paginate(20);
        return view('admin.users', compact('users', 'isSuperAdmin'));
    })->name('users');

    Route::post('/users/{user}/role', function (User $user, Request $request) {
        $isSuperAdmin = auth()->user()->role->level >= 100;

        // Non-super-admins cannot change super admin's role
        if (!$isSuperAdmin && $user->role && $user->role->level >= 100) {
            abort(403, 'Cannot modify super admin accounts.');
        }

        $request->validate(['role_id' => 'nullable|exists:roles,id']);

        // Non-super-admins cannot assign super admin role
        if (!$isSuperAdmin && $request->role_id) {
            $targetRole = \App\Models\Role::find($request->role_id);
            if ($targetRole && $targetRole->level >= 100) {
                abort(403, 'Cannot assign super admin role.');
            }
        }

        $user->update(['role_id' => $request->role_id]);
        return back()->with('success', 'User role updated.');
    })->name('users.role');

    // Screenings
    Route::get('/screenings', function () {
        $isSuperAdmin = auth()->user()->role->level >= 100;
        $screenings = Screening::with('user');
        if (!$isSuperAdmin) {
            $screenings->whereHas('user.role', fn($q) => $q->where('level', '<', 100));
        }
        $screenings = $screenings->latest()->paginate(20);
        return view('admin.screenings', compact('screenings'));
    })->name('screenings');

    // Reports
    Route::get('/reports', function () {
        $daily = Screening::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, type, COUNT(*) as count')
            ->groupBy('date', 'type')
            ->get()
            ->groupBy('date');
        return view('admin.reports', compact('daily'));
    })->name('reports');



    // Brand Settings
    Route::get('/brand', function () {
        $settings = Setting::allAsArray();
        return view('admin.brand', compact('settings'));
    })->name('brand');

    Route::post('/brand', function (Request $request) {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'accent_color' => 'nullable|string|max:7',
            'disclaimer_text' => 'nullable|string',
        ]);

        Setting::set('app_name', $request->app_name, 'string');
        Setting::set('primary_color', $request->primary_color ?? '#FFF9E8', 'color');
        Setting::set('secondary_color', $request->secondary_color ?? '#EBF4FF', 'color');
        Setting::set('accent_color', $request->accent_color ?? '#F3EEFF', 'color');
        Setting::set('disclaimer_text', $request->input('disclaimer_text', ''), 'string');

        // Update runtime config so it takes effect immediately
        config(['app.name' => $request->app_name]);

        // Social links
        $socials = ['social_instagram', 'social_twitter', 'social_tiktok', 'social_github'];
        foreach ($socials as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key, ''), 'string');
            }
        }

        config(['app.name' => $request->app_name]);

        return back()->with('success', 'Brand settings saved.');
    })->name('brand.update');

    Route::post('/brand/logo', function (Request $request) {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        // Delete old logo
        if (Setting::get('logo_path')) {
            Storage::disk('public')->delete(Setting::get('logo_path'));
        }

        $path = $request->file('logo')->store('brand', 'public');
        Setting::set('logo_path', $path, 'image');

        return response()->json(['success' => true, 'url' => Storage::disk('public')->url($path)]);
    })->name('brand.logo');

    Route::delete('/brand/logo', function () {
        if (Setting::get('logo_path')) {
            Storage::disk('public')->delete(Setting::get('logo_path'));
            Setting::set('logo_path', '', 'image');
        }
        return response()->json(['success' => true]);
    })->name('brand.logo.delete');

    // Hero image
    Route::post('/brand/hero', function (Request $request) {
        $request->validate([
            'hero_image' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:4096',
        ]);

        if (Setting::get('hero_image_path')) {
            Storage::disk('public')->delete(Setting::get('hero_image_path'));
        }

        $path = $request->file('hero_image')->store('brand/hero', 'public');
        Setting::set('hero_image_path', $path, 'image');

        return response()->json(['success' => true, 'url' => Storage::disk('public')->url($path)]);
    })->name('brand.hero');

    Route::delete('/brand/hero', function () {
        if (Setting::get('hero_image_path')) {
            Storage::disk('public')->delete(Setting::get('hero_image_path'));
            Setting::set('hero_image_path', '', 'image');
        }
        return response()->json(['success' => true]);
    })->name('brand.hero.delete');

    // API Key
    Route::post('/brand/api-key', function (Request $request) {
        $request->validate([
            'google_api_key' => 'nullable|string|max:255',
        ]);
        Setting::set('google_api_key', $request->input('google_api_key', ''), 'string');
        return back()->with('success', 'API key saved.');
    })->name('brand.api-key');

    // Roles & Permissions
    Route::get('/permissions', function () {
        $isSuperAdmin = auth()->user()->role->level >= 100;
        $roles = Role::withCount('users');
        if (!$isSuperAdmin) {
            $roles->where('level', '<', 100);
        }
        $roles = $roles->get();
        $allPermissions = [
            'users.view' => 'View users',
            'users.edit' => 'Edit users',
            'users.delete' => 'Delete users',
            'screenings.view' => 'View all screenings',
            'screenings.view_own' => 'View own screenings',
            'screenings.create' => 'Create screenings',
            'settings.view' => 'View settings',
            'settings.edit' => 'Edit settings',
            'reports.view' => 'View reports',
            'roles.manage' => 'Manage roles',
        ];
        return view('admin.permissions', compact('roles', 'allPermissions', 'isSuperAdmin'));
    })->name('permissions');

    Route::post('/permissions', function (Request $request) {
        $isSuperAdmin = auth()->user()->role->level >= 100;

        $request->validate([
            'name' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'level' => 'required|integer|min:0|max:100',
            'permissions' => 'nullable|array',
        ]);

        // Non-super-admins cannot create roles with level >= 100
        if (!$isSuperAdmin && $request->level >= 100) {
            abort(403, 'Cannot create super admin roles.');
        }

        Role::create($request->only('name', 'label', 'level', 'permissions'));
        return back()->with('success', 'Role created.');
    })->name('permissions.store');

    Route::put('/permissions/{role}', function (Role $role, Request $request) {
        $isSuperAdmin = auth()->user()->role->level >= 100;

        // Non-super-admins cannot edit super admin roles
        if (!$isSuperAdmin && $role->level >= 100) {
            abort(403, 'Cannot modify super admin role.');
        }

        $request->validate([
            'label' => 'required|string|max:255',
            'level' => 'required|integer|min:0|max:100',
            'permissions' => 'nullable|array',
        ]);

        // Non-super-admins cannot promote a role to level >= 100
        if (!$isSuperAdmin && $request->level >= 100) {
            abort(403, 'Cannot create super admin roles.');
        }

        $role->update($request->only('label', 'level', 'permissions'));
        return back()->with('success', 'Role updated.');
    })->name('permissions.update');
});
