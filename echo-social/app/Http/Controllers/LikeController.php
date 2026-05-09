<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Http\Request;

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