<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn() => Inertia::render('HomeView'))->name('home');
Route::get('/about', fn() => Inertia::render('AboutView'))->name('about');
Route::get('/history', fn() => Inertia::render('HistoryView'))->name('history');
Route::get('/runway', fn() => Inertia::render('RunwayView'))->name('runway');
Route::get('/style', fn() => Inertia::render('StyleView'))->name('style');
Route::get('/login', fn() => Inertia::render('LoginView'))->name('login');
Route::get('/profile', fn() => Inertia::render('ProfileView'));

Route::get('/posts', [PostController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user();
    });
});

Route::post('/update-profile', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'username' => 'required|string|max:255',
        'email' => 'required|email|max:255',
    ]);

    $user = $request->user();
    $user->username = $request->username;
    $user->email = $request->email;
    $user->save();

    return response()->json(['message' => 'Profile updated successfully']);
})->middleware('auth:sanctum');

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', function() {
    auth()->logout();
    return response()->json(['message' => 'Successfully logged out']);
});

Route::delete('/posts/{post}', [PostController::class, 'destroy']);

Route::get('posts/{postId}/comments', [CommentController::class, 'index']);
Route::post('posts/{postId}/comments', [CommentController::class, 'store'])->middleware('auth');

Route::post('posts/{postId}/reactions', [ReactionController::class, 'store']);
Route::get('posts/{postId}/reactions', [ReactionController::class, 'getReactions']);

Route::get('/categories', [CategoryController::class, 'index']);
