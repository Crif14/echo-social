<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EmbeddingService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.huggingface.key');
        $this->model = config('services.huggingface.model');
    }

    /*
    * Prende il testo di un post o di una ricerca e lo invia a Hugging Face 
    * che trasforma il testo in un Embedding, ovvero un array di numeri decimali che rappresenta il significato del testo
    */
    public function getEmbedding(string $text): ?array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post("https://router.huggingface.co/hf-inference/models/{$this->model}/pipeline/feature-extraction", [
                    'inputs' => $text,
                ]);

            $data = $response->json();

            if (!is_array($data)) return null;

            return $data;

        } catch (\Exception $e) {
            return null;
        }
    }

    /*
    * Capisce quanto due vettori di embedding sono simili tra loro, 
    *restituendo un punteggio da 0 a 1, dove 1 significa che i due vettori sono identici e 0 significa che sono completamente diversi
    */
    public function cosineSimilarity(array $a, array $b): float
    {
        $dot = 0;
        $normA = 0;
        $normB = 0;

        foreach ($a as $i => $val) {
            $dot += $val * ($b[$i] ?? 0);
            $normA += $val ** 2;
            $normB += ($b[$i] ?? 0) ** 2;
        }

        if ($normA == 0 || $normB == 0) return 0;

        return $dot / (sqrt($normA) * sqrt($normB));
    }

    /*
    * Prende la frase di ricerca e restituisce un array di punteggi di similarità tra la query e ogni post, ordinati dal più simile al meno simile
    */
    public function search(string $query, array $postEmbeddings): array
    {
        $queryEmbedding = $this->getEmbedding($query);

        if (!$queryEmbedding) return [];

        $scores = [];
        foreach ($postEmbeddings as $postId => $vector) {
            if (is_array($vector)) {
                $scores[$postId] = $this->cosineSimilarity($queryEmbedding, $vector);
            }
        }

        arsort($scores);
        return $scores;
    }
}