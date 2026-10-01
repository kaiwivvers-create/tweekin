<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isSuperAdmin() || $user->hasPermission('activity.view'), 403);

        $query = ActivityLog::with('user')->latest();

        if ($request->filled('action') && array_key_exists($request->action, ActivityLog::ACTIONS)) {
            $query->where('action', $request->action);
        }

        if ($request->filled('actor')) {
            $query->where('user_id', $request->actor);
        }

        if ($request->filled('subject')) {
            $query->where('subject_type', $request->subject);
        }

        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(function ($w) use ($term) {
                $w->where('description', 'like', "%{$term}%")
                    ->orWhere('subject_label', 'like', "%{$term}%")
                    ->orWhere('user_name', 'like', "%{$term}%")
                    ->orWhere('action', 'like', "%{$term}%");
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        $stats = [
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'week' => ActivityLog::where('created_at', '>=', now()->subWeek())->count(),
            'total' => ActivityLog::count(),
            'actors' => ActivityLog::whereNotNull('user_id')->distinct()->count('user_id'),
        ];

        $actorIds = ActivityLog::whereNotNull('user_id')->pluck('user_id')->unique();
        $actors = User::whereIn('id', $actorIds)->orderBy('name')->get();

        $subjects = ActivityLog::whereNotNull('subject_type')
            ->distinct()
            ->pluck('subject_type')
            ->mapWithKeys(fn ($type) => [$type => class_basename($type)]);

        return view('admin.activity', compact('logs', 'stats', 'actors', 'subjects'));
    }

    /** Wipe the activity trail. Super admin only. */
    public function clear(Request $request)
    {
        abort_unless($request->user()->isSuperAdmin(), 403, 'Only super admins can clear the activity log.');

        $count = ActivityLog::count();
        ActivityLog::query()->delete();

        ActivityLog::record('reset', "Cleared the activity log ({$count} entries removed).", [
            'subject_type' => ActivityLog::class,
            'subject_label' => 'Activity log',
        ]);

        return back()->with('success', "Cleared {$count} activity entries.");
    }
}
