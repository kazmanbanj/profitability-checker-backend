<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected string $baseUrl;

    protected string $apiKey;

    protected string $role;

    public function __construct()
    {
        $this->baseUrl = config('services.gemini.base_url');
        $this->apiKey = config('services.gemini.key');
        $this->role = config('services.gemini.role', 'user');
    }

    public function generateContent(string $prompt): ?string
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}?key={$this->apiKey}", [
            'contents' => [
                [
                    'role' => $this->role,
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
        ]);

        $data = $response->json();

        return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }
}
