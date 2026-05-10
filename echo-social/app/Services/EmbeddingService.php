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

    public function getEmbedding(string $text): array
    {
        $response = Http::withToken($this->apiKey)
            ->post("https://api-inference.huggingface.co/pipeline/feature-extraction/{$this->model}", [
                'inputs' => $text,
                'options' => ['wait_for_model' => true],
            ]);

        return $response->json();
    }

    public function cosineSimilarity(array $a, array $b): float
    {
        $dot = 0;
        $normA = 0;
        $normB = 0;

        foreach ($a as $i => $val) {
            $dot += $val * $b[$i];
            $normA += $val ** 2;
            $normB += $b[$i] ** 2;
        }

        if ($normA == 0 || $normB == 0) return 0;

        return $dot / (sqrt($normA) * sqrt($normB));
    }

    public function search(string $query, array $postEmbeddings): array
    {
        $queryEmbedding = $this->getEmbedding($query);

        $scores = [];
        foreach ($postEmbeddings as $postId => $vector) {
            $scores[$postId] = $this->cosineSimilarity($queryEmbedding, $vector);
        }

        arsort($scores);
        return $scores;
    }
}