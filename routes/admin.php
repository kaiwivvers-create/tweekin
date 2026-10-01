<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\DatabaseController;
use App\Http\Controllers\Admin\DataController;
use App\Http\Controllers\Admin\RoleController;
use App\Models\ActivityLog;
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

        // Super admins get an inline record editor, which needs the table's columns.
        $editor = $isSuperAdmin ? DataController::editorData('users') : [];

        return view('admin.users', compact('users', 'isSuperAdmin') + $editor);
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

        $beforeRole = $user->role->label ?? 'No role';
        $user->update(['role_id' => $request->role_id]);
        $afterRole = $user->fresh()->role->label ?? 'No role';

        ActivityLog::record('updated', "Changed {$user->name}'s role from \"{$beforeRole}\" to \"{$afterRole}\".", [
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'subject_label' => $user->name,
            'changes' => ['role' => ['before' => $beforeRole, 'after' => $afterRole]],
        ]);

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

        // Super admins get an inline record editor, which needs the table's columns.
        $editor = $isSuperAdmin ? DataController::editorData('screenings') : [];

        return view('admin.screenings', compact('screenings') + $editor);
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

    // Activity log
    Route::get('/activity', [ActivityController::class, 'index'])->name('activity');
    Route::delete('/activity', [ActivityController::class, 'clear'])->name('activity.clear');

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

        $before = Setting::allAsArray();

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

        $after = Setting::allAsArray();

        ActivityLog::record('updated', 'Updated the brand and app settings.', [
            'subject_type' => Setting::class,
            'subject_label' => 'Brand settings',
            'changes' => ActivityLog::diff($before, $after),
        ]);

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

        ActivityLog::record('updated', 'Uploaded a new brand logo.', [
            'subject_type' => Setting::class,
            'subject_label' => 'Logo',
            'changes' => ['logo_path' => ['before' => null, 'after' => $path]],
        ]);

        return response()->json(['success' => true, 'url' => Storage::disk('public')->url($path)]);
    })->name('brand.logo');

    Route::delete('/brand/logo', function () {
        if (Setting::get('logo_path')) {
            Storage::disk('public')->delete(Setting::get('logo_path'));
            Setting::set('logo_path', '', 'image');
        }

        ActivityLog::record('deleted', 'Removed the brand logo.', [
            'subject_type' => Setting::class,
            'subject_label' => 'Logo',
        ]);

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

        ActivityLog::record('updated', 'Uploaded a new hero image.', [
            'subject_type' => Setting::class,
            'subject_label' => 'Hero image',
            'changes' => ['hero_image_path' => ['before' => null, 'after' => $path]],
        ]);

        return response()->json(['success' => true, 'url' => Storage::disk('public')->url($path)]);
    })->name('brand.hero');

    Route::delete('/brand/hero', function () {
        if (Setting::get('hero_image_path')) {
            Storage::disk('public')->delete(Setting::get('hero_image_path'));
            Setting::set('hero_image_path', '', 'image');
        }

        ActivityLog::record('deleted', 'Removed the hero image.', [
            'subject_type' => Setting::class,
            'subject_label' => 'Hero image',
        ]);

        return response()->json(['success' => true]);
    })->name('brand.hero.delete');

    // API Key
    Route::post('/brand/api-key', function (Request $request) {
        $request->validate([
            'google_api_key' => 'nullable|string|max:255',
            'google_places_api_key' => 'nullable|string|max:255',
        ]);

        // Blank means "leave as-is" so saving one key doesn't wipe the other,
        // since both fields are masked password inputs.
        $changed = [];
        if ($request->filled('google_api_key')) {
            Setting::set('google_api_key', $request->input('google_api_key'), 'string');
            $changed[] = 'google_api_key';
        }
        if ($request->filled('google_places_api_key')) {
            Setting::set('google_places_api_key', $request->input('google_places_api_key'), 'string');
            $changed[] = 'google_places_api_key';
        }

        if ($changed) {
            ActivityLog::record('updated', 'Updated the stored API keys.', [
                'subject_type' => Setting::class,
                'subject_label' => 'API keys',
                'changes' => collect($changed)->mapWithKeys(fn ($key) => [$key => ['before' => '••••••', 'after' => '••••••']])->all(),
            ]);
        }

        return back()->with('success', 'API keys saved.');
    })->name('brand.api-key');

    // Roles & Permissions
    Route::get('/permissions', [RoleController::class, 'index'])->name('permissions');
    Route::post('/permissions', [RoleController::class, 'store'])->name('permissions.store');
    Route::put('/permissions/{role}', [RoleController::class, 'update'])->name('permissions.update');
    Route::delete('/permissions/{role}', [RoleController::class, 'destroy'])->name('permissions.destroy');

    // Database tools (export / import / reset) — super admin only
    Route::get('/database', [DatabaseController::class, 'index'])->name('database');
    Route::get('/database/export', [DatabaseController::class, 'export'])->name('database.export');
    Route::post('/database/import', [DatabaseController::class, 'import'])->name('database.import');
    Route::post('/database/reset', [DatabaseController::class, 'reset'])->name('database.reset');

    // Raw data editor + SQL console — super admin only
    Route::get('/data', [DataController::class, 'index'])->name('data');
    Route::post('/data/query', [DataController::class, 'query'])->name('data.query');
    Route::get('/data/{table}', [DataController::class, 'browse'])->name('data.browse');
    Route::get('/data/{table}/{id}', [DataController::class, 'show'])->name('data.show');
    Route::put('/data/{table}/{id}', [DataController::class, 'update'])->name('data.update');
    Route::delete('/data/{table}/{id}', [DataController::class, 'destroy'])->name('data.destroy');
});
