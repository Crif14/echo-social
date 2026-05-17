<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    protected $table = 'postLikes';

    public $timestamps = false;

    protected $fillable = ['userId', 'postId'];

    //Collega direttamente il singolo Like sia all'utente che al post.
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'postId');
    }
}