<?php

use App\Http\Controllers\InfoController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn() => Inertia::render('HomeView'))->name('home');
Route::get('/about', fn() => Inertia::render('AboutView'))->name('about');
Route::get('/history', fn() => Inertia::render('HistoryView'))->name('history');
Route::get('/runway', fn() => Inertia::render('RunwayView'))->name('runway');
Route::get('/style', fn() => Inertia::render('StyleView'))->name('style');
Route::get('/login', fn() => Inertia::render('LoginView'))->name('login');
Route::get('/profile', fn() => Inertia::render('ProfileView'));

Route::get('/info', [InfoController::class, 'getInfo']);
Route::get('/posts', [PostController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user();
    });
});

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', function() {
    auth()->logout();
    return response()->json(['message' => 'Вы вышли из системы']);
});
