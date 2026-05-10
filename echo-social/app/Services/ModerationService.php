<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ModerationService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.groq.key');
        $this->model = config('services.groq.model');
    }

    public function isAllowed(string $text): bool
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Sei un moderatore di contenuti. Rispondi SOLO con "ok" se il testo è accettabile, oppure "no" se contiene contenuti offensivi, violenti, sessualmente espliciti o spam. Nessuna altra risposta.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $text
                        ]
                    ],
                    'max_tokens' => 5,
                    'temperature' => 0,
                ]);

            $answer = strtolower(trim($response->json('choices.0.message.content')));
            return $answer === 'ok';

        } catch (\Exception $e) {
            // Se Groq non è disponibile, permettiamo il contenuto
            return true;
        }
    }

    public function getReason(string $text): string
    {
        return 'Contenuto non consentito dalla moderazione automatica.';
    }
}