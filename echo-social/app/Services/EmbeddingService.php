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