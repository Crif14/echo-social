<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Embedding extends Model
{
    protected $table = 'embeddings';

    public $timestamps = false;

    protected $fillable = ['postId', 'vector'];

    protected $casts = [
        'vector' => 'array',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'postId');
    }
}