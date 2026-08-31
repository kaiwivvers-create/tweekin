<?php

use App\Http\Controllers\ProfileController;
use App\Models\Screening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Dashboard (auth required)
Route::get('/dashboard', function () {
    $screenings = Screening::forCurrentUser(session()->getId())->latest()->get();
    $counts = [
        'total' => $screenings->count(),
        'physical' => $screenings->where('type', 'physical')->count(),
        'mental' => $screenings->where('type', 'mental')->count(),
        'other' => $screenings->where('type', 'other')->count(),
    ];
    return view('dashboard', compact('screenings', 'counts'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');
});

// Save screening result
Route::post('/screenings', function (Request $request) {
    $validated = $request->validate([
        'type' => 'required|in:physical,mental,other',
        'title' => 'required|string|max:255',
        'data' => 'required|array',
        'severity' => 'nullable|integer|min:1|max:5',
        'assessment' => 'nullable|string',
    ]);

    Screening::create([
        'user_id' => auth()->id(),
        'session_id' => auth()->check() ? null : session()->getId(),
        'type' => $validated['type'],
        'title' => $validated['title'],
        'data' => $validated['data'],
        'severity' => $validated['severity'] ?? null,
        'assessment' => $validated['assessment'] ?? null,
    ]);

    return response()->json(['success' => true]);
})->name('screenings.store');

// View single screening
Route::get('/screenings/{screening}', function (Screening $screening) {
    if (auth()->check()) {
        abort_unless($screening->user_id === auth()->id(), 403);
    } else {
        abort_unless($screening->session_id === session()->getId(), 403);
    }
    return view('screenings.show', compact('screening'));
})->name('screenings.show');

// Physical symptom flow
Route::prefix('physical')->name('physical.')->group(function () {
    Route::get('/', function () {
        return view('physical.index');
    })->name('index');

    Route::get('/questions', function () {
        return view('physical.questions');
    })->name('questions');

    Route::get('/results', function () {
        return view('physical.results');
    })->name('results');
});

// Mental health flow
Route::prefix('mental')->name('mental.')->group(function () {
    Route::get('/', function () {
        return view('mental.index');
    })->name('index');

    Route::get('/mode', function () {
        return view('mental.mode');
    })->name('mode');

    Route::get('/understand', function () {
        return view('mental.understand');
    })->name('understand');

    Route::get('/vent', function () {
        return view('mental.vent');
    })->name('vent');
});

// Other / Not sure flow
Route::prefix('other')->name('other.')->group(function () {
    Route::get('/', function () {
        return view('other.index');
    })->name('index');

    Route::get('/results', function () {
        return view('other.results');
    })->name('results');
});

// AI Chat API
Route::post('/api/chat', function (Request $request) {
    $request->validate([
        'message' => 'required|string|max:1000',
        'history' => 'nullable|array',
        'context' => 'required|array',
    ]);

    $gemini = new \App\Services\GeminiChatService();

    if (!$gemini->isConfigured()) {
        return response()->json([
            'response' => null,
            'error' => 'AI is not configured. An admin needs to add a Google AI API key in Brand Settings.',
        ]);
    }

    $response = $gemini->chat(
        $request->input('history', []),
        $request->input('message'),
        $request->input('context')
    );

    if ($response === null) {
        return response()->json([
            'response' => null,
            'error' => 'Could not get a response. Please try again.',
        ]);
    }

    return response()->json(['response' => $response]);
})->name('api.chat');

// Auth routes (Breeze)
require __DIR__.'/auth.php';

// Admin routes
require __DIR__.'/admin.php';
