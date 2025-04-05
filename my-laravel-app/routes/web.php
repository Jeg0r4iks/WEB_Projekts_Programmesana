<?php
use App\Http\Controllers\InfoController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
*/

Route::get('/', function () {
    return Inertia::render('HomeView');
})->name('home');

Route::get('/about', function () {
    return Inertia::render('AboutView');
})->name('about');

Route::get('/history', function () {
    return Inertia::render('HistoryView');
})->name('history');

Route::get('/runway', function () {
    return Inertia::render('RunwayView');
})->name('runway');

Route::get('/style', function () {
    return Inertia::render('StyleView');
})->name('style');

Route::get('/login', function () {
    return Inertia::render('LoginView');
})->name('login');

Route::get('/info', [InfoController::class, 'getInfo']);

Route::get('/api/posts', [PostController::class, 'index']);

Route::post('/api/posts', [PostController::class, 'store']);
