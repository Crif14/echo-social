<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Embedding;
use App\Services\EmbeddingService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Recupera la frase cercata
        $query = $request->input('q');
        $posts = collect();

        if ($query) {
            $embeddingService = new EmbeddingService();

            // Recupera tutti gli embeddings salvati nel database
            $embeddings = Embedding::with('post.user', 'post.comments', 'post.likes')
                ->get();

            if ($embeddings->isEmpty()) {
                return view('search.index', compact('posts', 'query'))
                    ->with('warning', 'Nessun embedding trovato. Pubblica alcuni post prima!');
            }

            // Costruisce un array di embedding dei post, con chiave l'id del post e valore il vettore di embedding
            $postEmbeddings = $embeddings->mapWithKeys(fn($e) => [
                $e->postId => $e->vector
            ])->toArray();

            // Calcola similarità tra la query e ogni post
            $scores = $embeddingService->search($query, $postEmbeddings);

            // Prende i top 10 post con score > 0.3
            $topPostIds = array_keys(array_filter($scores, fn($s) => $s > 0.3));
            $topPostIds = array_slice($topPostIds, 0, 10);

            // Recupera i post in ordine di similarità e filtra quelli flaggati
            $posts = collect($topPostIds)->map(function ($postId) use ($embeddings) {
                return $embeddings->firstWhere('postId', $postId)?->post;
            })->filter()->where('isFlagged', false);
        }

        return view('search.index', compact('posts', 'query'));
    }

    //ADMINISTRATOR ONLY
    //Trova tutti i post non censurati che non hanno ancora un embedding e genera l'embedding per ciascuno di essi, salvandolo nel database
    public function generateEmbeddings()
    {
        $embeddingService = new EmbeddingService();

        $posts = Post::where('isFlagged', false)
            ->doesntHave('embedding')
            ->get();

        foreach ($posts as $post) {
            $vector = $embeddingService->getEmbedding($post->body);

            Embedding::create([
                'postId' => $post->id,
                'vector' => $vector,
            ]);
        }

        return redirect()->route('search.index')
            ->with('success', "Generati embeddings per {$posts->count()} post!");
    }
}