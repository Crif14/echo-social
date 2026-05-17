<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Embedding extends Model
{
    protected $table = 'embeddings';

    public $timestamps = false;

    protected $fillable = ['postId', 'vector'];

    // Cast del campo vector come array, in modo che venga salvato come JSON nel database e restituito come array quando viene recuperato
    protected $casts = [
        'vector' => 'array',
    ];

    // Relazione con il modello Post, un embedding appartiene a un post
    public function post()
    {
        return $this->belongsTo(Post::class, 'postId');
    }
}