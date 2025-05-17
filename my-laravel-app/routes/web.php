<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\{
    PostController,
    RegisterController,
    LoginController,
    CommentController,
    ReactionController,
    CategoryController
};
use Inertia\Inertia;

// Страницы
Route::get('/', fn() => Inertia::render('HomeView'))->name('home');
Route::get('/about', fn() => Inertia::render('AboutView'))->name('about');
Route::get('/history', fn() => Inertia::render('HistoryView'))->name('history');
Route::get('/runway', fn() => Inertia::render('RunwayView'))->name('runway');
Route::get('/style', fn() => Inertia::render('StyleView'))->name('style');
Route::get('/login', fn() => Inertia::render('LoginView'))->name('login');
Route::get('/profile', fn() => Inertia::render('ProfileView'));

// Публичные запросы
Route::get('/posts', [PostController::class, 'index']);
Route::get('posts/{postId}/comments', [CommentController::class, 'index']);
Route::get('posts/{postId}/reactions', [ReactionController::class, 'getReactions']);
Route::get('/categories', [CategoryController::class, 'index']);

// Аутентификация (всё в web middleware)
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);

// Защищённые маршруты
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/posts', [PostController::class, 'store']);
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
