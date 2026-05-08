<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user', 'comments', 'likes')
            ->where('isFlagged', false)
            ->latest()
            ->get();

        return view('posts.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'body' => 'required|min:3|max:500',
        ]);

        Post::create([
            'userId' => auth()->id(),
            'body' => $request->body,
        ]);

        return redirect()->route('posts.index');
    }

    public function destroy(Post $post)
    {
        if ($post->userId !== auth()->id()) {
            abort(403);
        }

        $post->delete();

        return redirect()->route('posts.index');
    }
}