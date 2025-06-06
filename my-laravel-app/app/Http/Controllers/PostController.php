<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user','categories','reactions']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $ids = $request->input('category_ids', []);
        $totalCategories = \App\Models\Category::count();
        if (is_array($ids) && count($ids) > 0 && count($ids) < $totalCategories) {
            foreach ($ids as $catId) {
                $query->whereHas('categories', fn($q) =>
                $q->where('categories.id', $catId)
                );
            }
        }

        if ($request->filled('search')) {
            $query->where(fn($q) =>
            $q->where('title', 'like', "%{$request->search}%")
                ->orWhere('content', 'like', "%{$request->search}%")
            );
        }

        $posts = $query->latest()->get();

        return response()->json($posts);
    }

    public function show(Post $post)
    {
        $post->load(['user','categories','reactions']);
        return response()->json($post);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'content'        => 'required|string',
            'category_ids'   => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'image'          => 'nullable|image|max:10240',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts','public');
        }

        $post = Post::create([
            'title'   => $data['title'],
            'content' => $data['content'],
            'user_id' => auth()->id(),
            'image'   => $path,
        ]);

        $post->categories()->sync($data['category_ids']);
        $post->load(['user','categories','reactions']);
        $post->image = $post->image_url;

        return response()->json([
            'message' => 'Post created successfully',
            'post'    => $post
        ], 201);
    }

    public function update(Request $request, Post $post)
    {
        if (auth()->id() !== $post->user_id && ! auth()->user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'content'        => 'required|string',
            'category_ids'   => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'image'          => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $data['image'] = $request->file('image')->store('posts','public');
        }

        $post->update([
            'title'   => $data['title'],
            'content' => $data['content'],
            'image'   => $data['image'] ?? $post->image,
        ]);

        if (isset($data['category_ids'])) {
            $post->categories()->sync($data['category_ids']);
        }

        $post->load(['user','categories','reactions']);
        $post->image = $post->image_url;

        return response()->json([
            'message' => 'Post updated successfully',
            'post'    => $post
        ]);
    }

    public function destroy(Post $post)
    {
        if (auth()->id() !== $post->user_id && ! auth()->user()->is_admin) {
            return response()->json(['error'=>'Unauthorized'],403);
        }

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return response()->json(['message'=>'Post deleted successfully']);
    }
}
