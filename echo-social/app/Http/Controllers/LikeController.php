<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Http\Request;

//Controlla i like dei post, agendo di conseguenza quando un utente clicca sul pulsante like
class LikeController extends Controller
{
    public function toggle(Post $post)
    {
        $userId = auth()->id();

        $like = PostLike::where('postId', $post->id)
            ->where('userId', $userId)
            ->first();

        if ($like) {
            $like->delete();
        } else {
            PostLike::create([
                'postId' => $post->id,
                'userId' => $userId,
            ]);
        }

        return redirect()->route('posts.index');
    }
}