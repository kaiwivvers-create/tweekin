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

    $screening = Screening::create([
        'user_id' => auth()->id(),
        'session_id' => auth()->check() ? null : session()->getId(),
        'type' => $validated['type'],
        'title' => $validated['title'],
        'data' => $validated['data'],
        'severity' => $validated['severity'] ?? null,
        'assessment' => $validated['assessment'] ?? null,
    ]);

    // Create notification for logged-in users
    if (auth()->check() && $screening->severity) {
        \App\Models\UserNotification::forScreeningComplete($screening);
    }

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

// Screening comparison
Route::middleware('auth')->prefix('compare')->name('compare.')->group(function () {
    Route::get('/', function () {
        $screenings = Screening::forCurrentUser()->latest()->get();
        return view('compare.index', compact('screenings'));
    })->name('index');

    Route::post('/', function (Request $request) {
        $ids = $request->input('screenings', []);
        if (count($ids) < 2) {
            return redirect()->route('compare.index')->with('error', 'Select at least 2 screenings to compare.');
        }
        $screenings = Screening::whereIn('id', $ids)
            ->where('user_id', auth()->id())
            ->get();
        return view('compare.show', compact('screenings'));
    })->name('show');
});

// Notifications
Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', function () {
        $notifications = \App\Models\UserNotification::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);
        return view('notifications.index', compact('notifications'));
    })->name('index');

    Route::post('/{notification}/read', function (\App\Models\UserNotification $notification) {
        abort_unless($notification->user_id === auth()->id(), 403);
        $notification->markRead();
        return response()->json(['success' => true]);
    })->name('read');

    Route::post('/read-all', function () {
        \App\Models\UserNotification::where('user_id', auth()->id())
            ->where('read', false)
            ->update(['read' => true]);
        return response()->json(['success' => true]);
    })->name('readAll');

    Route::get('/unread-count', function () {
        return response()->json(['count' => \App\Models\UserNotification::unreadCount(auth()->id())]);
    })->name('unreadCount');
});

// Physical symptom flow
Route::prefix('physical')->name('physical.')->group(function () {
    Route::get('/', function () {
        return view('physical.index');
    })->name('index');

    Route::get('/questions', function () {
        return view('physical.questions');
    })->name('questions');

    Route::get('/details', function () {
        return view('physical.details');
    })->name('details');

    Route::get('/results', function () {
        return view('physical.results');
    })->name('results');
});

// Mental health flow
Route::prefix('mental')->name('mental.')->group(function () {
    Route::get('/', function () {
        return view('mental.index');
    })->name('index');

    Route::get('/details', function () {
        return view('mental.details');
    })->name('details');

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

// AI Section Generation API
Route::post('/api/generate-section', function (Request $request) {
    $request->validate([
        'section' => 'required|string|in:patterns,strategies,what-to-tell,correlation,specialists,all',
        'context' => 'required|array',
    ]);

    $gemini = new \App\Services\GeminiChatService();

    if (!$gemini->isConfigured()) {
        return response()->json(['error' => 'AI is not configured.'], 503);
    }

    $response = $gemini->generateSection(
        $request->input('section'),
        $request->input('context')
    );

    if ($response === null) {
        return response()->json(['error' => 'Could not generate section. Please try again.'], 500);
    }

    return response()->json(['content' => $response]);
})->name('api.generate-section');

// Google Places photo redirect — keeps the API key out of the browser.
Route::get('/api/place-photo', function (Request $request) {
    $encoded = (string) $request->query('ref', '');
    $photoName = base64_decode(strtr($encoded, '-_', '+/'), true);

    if (!$photoName || !str_starts_with($photoName, 'places/')) {
        abort(404);
    }

    $url = (new \App\Services\GooglePlacesService())->resolvePhotoUrl($photoName);

    if ($url === null) {
        abort(404);
    }

    return redirect()->away($url);
})->name('api.place-photo');

// Nearby care API (location-aware)
Route::post('/api/nearby-care', function (Request $request) {
    $request->validate([
        'context' => 'required|array',
        'location' => 'nullable|string|max:200',
        'query' => 'nullable|string|max:200',
        'lat' => 'nullable|numeric|between:-90,90',
        'lon' => 'nullable|numeric|between:-180,180',
    ]);

    $gemini = new \App\Services\GeminiChatService();

    $context = $request->input('context');
    if ($request->filled('location')) {
        $context['location'] = $request->input('location');
    }

    $type = $context['type'] ?? 'general';
    $lat = $request->filled('lat') ? (float) $request->input('lat') : null;
    $lon = $request->filled('lon') ? (float) $request->input('lon') : null;
    $locationSource = ($lat !== null && $lon !== null) ? 'gps' : null;
    $geocodeFailed = false;

    // An area the user typed themselves beats any guess, so try it first.
    if (($lat === null || $lon === null) && $request->filled('query')) {
        $geocoded = (new \App\Services\GeocodingService())->search((string) $request->input('query'));

        if ($geocoded !== null) {
            $lat = $geocoded['lat'];
            $lon = $geocoded['lon'];
            $locationSource = 'query';
            $context['location'] = $geocoded['label'];
        } else {
            $geocodeFailed = true;
        }
    }

    // Still nothing: no GPS (permission denied, insecure origin, desktop with
    // location off) and no usable typed area, so fall back to a coarse network
    // location. Anything is better than guessing at facility names.
    if ($lat === null || $lon === null) {
        $coarse = (new \App\Services\IpLocationService())->locate($request->ip());

        if ($coarse !== null) {
            $lat = $coarse['lat'];
            $lon = $coarse['lon'];
            $locationSource = 'ip';

            if (empty($context['location'])) {
                $context['location'] = $coarse['label'] ?: null;
            }
        }
    }

    // Real facilities. Google Places is preferred when a key is configured because
    // it supplies a photo for almost every place; OpenStreetMap is the keyless
    // fallback and also covers the case where Places finds nothing. Neither needs
    // the AI to be configured.
    $places = [];
    $placesApi = new \App\Services\GooglePlacesService();

    if ($lat !== null && $lon !== null) {
        if ($placesApi->isConfigured()) {
            $places = $placesApi->searchNearby($lat, $lon, $type);
        }

        if (count($places) === 0) {
            $places = (new \App\Services\OverpassService())->findNearby($lat, $lon, $type);
        }
    }

    // Nothing real found: return no facility cards at all. This card promises care
    // near the user, so invented facility names would be actively misleading —
    // real helplines are the only honest thing left to show.
    if (count($places) === 0) {
        return response()->json([
            'facilities' => [],
            'helplines' => $gemini->localHelplines($context) ?? $gemini->fallbackHelplines($type),
            'location' => $context['location'] ?? null,
            'locationSource' => $locationSource,
            'geocodeFailed' => $geocodeFailed,
        ]);
    }

    $annotations = $gemini->annotatePlaces($places, $context) ?? ['notes' => [], 'helplines' => []];

    // The model returns notes in its own order of fit, so use that as the ranking
    // and drop anything it did not consider worth recommending.
    $ranked = [];
    foreach ($annotations['notes'] as $note) {
        $index = (int) ($note['index'] ?? -1);
        if (!isset($places[$index])) {
            continue;
        }
        $ranked[] = ['place' => $places[$index], 'note' => $note];
    }

    if (count($ranked) === 0) {
        foreach (array_slice($places, 0, 6) as $place) {
            $ranked[] = ['place' => $place, 'note' => []];
        }
    }

    $ranked = array_slice($ranked, 0, 6);

    // Resolve photos. Google photos go through our own redirect so the API key
    // stays server-side; Wikimedia is the fallback and is cached for a week.
    $images = new \App\Services\PlaceImageService();
    $imageUrls = [];
    foreach ($ranked as $i => $entry) {
        $photoRef = $entry['place']['photoRef'] ?? null;

        $imageUrls[$i] = $photoRef
            ? route('api.place-photo', ['ref' => rtrim(strtr(base64_encode($photoRef), '+/', '-_'), '=')])
            : $images->resolve(
                $entry['place']['name'],
                $entry['place']['lat'],
                $entry['place']['lon'],
                $context['location'] ?? null,
                $entry['place']['wikipedia'] ?? null,
                $entry['place']['wikidata'] ?? null
            );
    }

    $facilities = [];
    foreach ($ranked as $i => $entry) {
        $place = $entry['place'];
        $note = $entry['note'];

        $facilities[] = [
            'name' => $place['name'],
            'type' => $place['type'],
            'address' => $place['address'],
            'phone' => $place['phone'],
            'website' => $place['website'] ?? '',
            'mapsUrl' => $place['mapsUrl'] ?? '',
            'distanceKm' => $place['distanceKm'],
            'lat' => $place['lat'],
            'lon' => $place['lon'],
            'rating' => $place['rating'] ?? null,
            'ratingCount' => $place['ratingCount'] ?? null,
            'goodFor' => (string) ($note['goodFor'] ?? ''),
            'why' => (string) ($note['why'] ?? ''),
            'image' => $imageUrls[$i] ?? null,
        ];
    }

    $helplines = $annotations['helplines'];
    if (count($helplines) === 0) {
        $helplines = $gemini->fallbackHelplines($type);
    }

    return response()->json([
        'facilities' => $facilities,
        'helplines' => $helplines,
        'location' => $context['location'] ?? null,
        'locationSource' => $locationSource,
        'geocodeFailed' => $geocodeFailed,
    ]);
})->name('api.nearby-care');

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
