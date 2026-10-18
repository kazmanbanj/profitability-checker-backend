<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    protected string $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';

    protected string $apiKey;

    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.groq.key', '');
        $this->model = config('services.groq.model', 'openai/gpt-oss-20b');
    }

    public function generateContent(string $prompt): ?string
    {
        if (! $this->apiKey) {
            Log::warning('Groq API call skipped: API key is not configured.');

            return null;
        }

        $response = Http::timeout(30)->withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type'  => 'application/json',
        ])->post($this->baseUrl, [
            'model' => $this->model,
            'messages' => [
                [
                    'role'    => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature'     => 0.7,
            'response_format' => ['type' => 'json_object'],
        ]);

        $data = $response->json();

        if ($response->failed()) {
            Log::error('Groq API request failed.', [
                'status' => $response->status(),
                'body'   => $data,
            ]);

            return null;
        }

        $text = $data['choices'][0]['message']['content'] ?? null;

        if ($text === null) {
            Log::warning('Groq API returned an unexpected response structure.', [
                'status' => $response->status(),
                'body'   => $data,
            ]);
        }

        return $text;
    }
}
