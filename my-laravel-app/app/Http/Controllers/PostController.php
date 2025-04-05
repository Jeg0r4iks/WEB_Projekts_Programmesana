<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // Получение всех постов
    public function index()
    {
        $posts = Post::all();
        return response()->json($posts);  // Возвращаем все посты в формате JSON
    }

    // Создание нового поста
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = Post::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        return response()->json($post, 201);  // Возвращаем созданный пост
    }
}
