<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use Inertia\Inertia;

Route::get('/', fn() => Inertia::render('HomeView'))->name('home');
Route::get('/about', fn() => Inertia::render('AboutView'))->name('about');
Route::get('/history', fn() => Inertia::render('HistoryView'))->name('history');
Route::get('/runway', fn() => Inertia::render('RunwayView'))->name('runway');
Route::get('/style', fn() => Inertia::render('StyleView'))->name('style');
Route::get('/login', fn() => Inertia::render('LoginView'))->name('login');
Route::get('/profile', fn() => Inertia::render('ProfileView'));

Route::get('/posts', [PostController::class, 'index']);
Route::get('posts/{postId}/comments', [CommentController::class, 'index']);
Route::get('posts/{postId}/reactions', [ReactionController::class, 'getReactions']);
Route::get('/categories', [CategoryController::class, 'index']);

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);

    Route::post('posts/{postId}/comments', [CommentController::class, 'store']);
    Route::post('posts/{postId}/reactions', [ReactionController::class, 'store']);

    Route::get('/user', fn(Request $request) => $request->user());

    Route::post('/update-profile', function (Request $request) {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $user = $request->user();
        $user->username = $request->username;
        $user->email = $request->email;
        $user->save();

        return response()->json(['message' => 'Profile updated successfully']);
    });
});

Route::middleware(['auth:sanctum','admin'])
    ->prefix('admin')
    ->group(function(){
        Route::put('/posts/{post}', [AdminPostController::class, 'update']);
        Route::delete('/posts/{post}', [AdminPostController::class, 'destroy']);
    });
