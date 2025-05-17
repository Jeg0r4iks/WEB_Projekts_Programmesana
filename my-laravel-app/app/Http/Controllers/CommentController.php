<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index($postId)
    {
        $comments = Comment::where('post_id', $postId)->with('user')->get();
        return response()->json($comments);
    }

    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $post = Post::find($postId);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $comment = Comment::create([
            'content' => $request->content,
            'user_id' => Auth::id(),
            'post_id' => $postId,
        ]);

        return response()->json($comment, 201);
    }

}
