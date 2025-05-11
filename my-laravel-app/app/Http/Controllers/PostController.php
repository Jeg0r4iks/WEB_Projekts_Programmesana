<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::query();

        // Фильтрация по категории, если категория передана в запросе
        if ($request->has('category_id') && $request->category_id != '') {
            $query->whereHas('categories', function ($query) use ($request) {
                $query->where('categories.id', $request->category_id);
            });
        }

        // Получаем посты, вместе с количеством реакций
        $posts = $query->with('user:id,username', 'categories:id,name')->get();

        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id'
        ]);

        $post = Post::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'user_id' => auth()->id(),
        ]);

        $post->categories()->sync($validated['category_ids']);

        return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }


}
