<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;

class GeminiProvider implements AiProviderInterface
{
    public function ask(string $message, array $conversationHistory = [], string $systemPrompt = ''): ?string
    {
        $apiKey = config('services.gemini.key');
        if (!$apiKey) return null;

        try {
            $contents = [];

            // Build conversation history for Gemini format
            foreach (array_slice($conversationHistory, -20) as $msg) {
                $contents[] = [
                    'role'  => $msg['role'] === 'user' ? 'user' : 'model',
                    'parts' => [['text' => $msg['content']]],
                ];
            }

            // Current user message (prepend system prompt to first message if needed)
            $userText = $message;
            if ($systemPrompt && empty($conversationHistory)) {
                $userText = $systemPrompt . "\n\nUser Question: " . $message;
            }

            $contents[] = [
                'role'  => 'user',
                'parts' => [['text' => $userText]],
            ];

            $payload = [
                'contents'         => $contents,
                'generationConfig' => [
                    'temperature'    => 0.7,
                    'topK'           => 40,
                    'topP'           => 0.95,
                    'maxOutputTokens' => 1024,
                ],
            ];

            // Add system instruction if supported
            if ($systemPrompt) {
                $payload['systemInstruction'] = [
                    'parts' => [['text' => $systemPrompt]],
                ];
            }

            $response = Http::timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                $payload
            );

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }

            \Log::warning('Gemini API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('Gemini provider error: ' . $e->getMessage());
        }

        return null;
    }

    public function name(): string
    {
        return 'Gemini AI';
    }

    public function isAvailable(): bool
    {
        return !empty(config('services.gemini.key'));
    }
}
