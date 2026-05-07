<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    public function posts()
    {
        return $this->hasMany(Post::class, 'userId');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'userId');
    }

    public function dailyTopics()
    {
        return $this->hasMany(DailyTopic::class, 'userId');
    }

    public function likedPosts()
    {
        return $this->belongsToMany(Post::class, 'postLikes', 'userId', 'postId');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}