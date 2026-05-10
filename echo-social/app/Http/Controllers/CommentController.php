<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Services\ModerationService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'body' => 'required|min:1|max:300',
        ]);

        $moderation = new ModerationService();

        if (!$moderation->isAllowed($request->body)) {
            return back()->withErrors([
                'body_' . $post->id => 'Il commento è stato bloccato dalla moderazione.'
            ])->withInput();
        }

        Comment::create([
            'postId' => $post->id,
            'userId' => auth()->id(),
            'body' => $request->body,
        ]);

        return redirect()->route('posts.index');
    }

    public function destroy(Comment $comment)
    {
        if ($comment->userId !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $comment->delete();

        return redirect()->route('posts.index');
    }
}