<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIBudgetService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.ai.url'), '/');
    }

    public function generateSuggestions(array $eventData): array
    {
        $response = Http::timeout(90)
            ->acceptJson()
            ->post("{$this->baseUrl}/generate-budget", $eventData);

        if (!$response->successful()) {
            Log::error('AI Budget Service Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('AI service returned error: ' . $response->status());
        }

        return $response->json();
    }
}