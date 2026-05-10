<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\DailyTopic;
use App\Services\ModerationService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user', 'comments', 'likes')
            ->where('isFlagged', false)
            ->latest()
            ->get();

        $todayTopic = DailyTopic::today()->with('user')->first();

        return view('posts.index', compact('posts', 'todayTopic'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'body' => 'required|min:3|max:500',
        ]);

        $moderation = new ModerationService();

        if (!$moderation->isAllowed($request->body)) {
            return back()->withErrors([
                'body' => 'Il tuo post è stato bloccato dalla moderazione automatica.'
            ])->withInput();
        }

        Post::create([
            'userId' => auth()->id(),
            'body' => $request->body,
        ]);

        return redirect()->route('posts.index');
    }

    public function destroy(Post $post)
    {
        if ($post->userId !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $post->delete();

        return redirect()->route('posts.index');
    }
}