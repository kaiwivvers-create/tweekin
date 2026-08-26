<?php

use App\Models\User;
use App\Models\Screening;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', function () {
        $stats = [
            'total_users' => User::count(),
            'total_screenings' => Screening::count(),
            'physical_screenings' => Screening::where('type', 'physical')->count(),
            'mental_screenings' => Screening::where('type', 'mental')->count(),
            'other_screenings' => Screening::where('type', 'other')->count(),
            'recent_users' => User::latest()->take(5)->get(),
            'recent_screenings' => Screening::latest()->take(5)->get(),
        ];
        return view('admin.dashboard', $stats);
    })->name('dashboard');

    // Users
    Route::get('/users', function () {
        $users = User::with('role')->latest()->paginate(20);
        return view('admin.users', compact('users'));
    })->name('users');

    Route::post('/users/{user}/role', function (User $user, Request $request) {
        $request->validate(['role_id' => 'nullable|exists:roles,id']);
        $user->update(['role_id' => $request->role_id]);
        return back()->with('success', 'User role updated.');
    })->name('users.role');

    // Screenings
    Route::get('/screenings', function () {
        $screenings = Screening::with('user')->latest()->paginate(20);
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

    // App Settings
    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');

    Route::post('/settings', function (Request $request) {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'disclaimer_text' => 'nullable|string',
        ]);
        config(['app.name' => $request->app_name]);
        // In a real app, save to database/config file
        return back()->with('success', 'Settings saved.');
    })->name('settings.update');

    // Brand Settings
    Route::get('/brand', function () {
        return view('admin.brand');
    })->name('brand');

    // Roles & Permissions
    Route::get('/permissions', function () {
        $roles = Role::withCount('users')->get();
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
        return view('admin.permissions', compact('roles', 'allPermissions'));
    })->name('permissions');

    Route::post('/permissions', function (Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'level' => 'required|integer|min:0|max:100',
            'permissions' => 'nullable|array',
        ]);
        Role::create($request->only('name', 'label', 'level', 'permissions'));
        return back()->with('success', 'Role created.');
    })->name('permissions.store');

    Route::put('/permissions/{role}', function (Role $role, Request $request) {
        $request->validate([
            'label' => 'required|string|max:255',
            'level' => 'required|integer|min:0|max:100',
            'permissions' => 'nullable|array',
        ]);
        $role->update($request->only('label', 'level', 'permissions'));
        return back()->with('success', 'Role updated.');
    })->name('permissions.update');
});
