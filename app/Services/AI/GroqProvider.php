<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;

class GroqProvider implements AiProviderInterface
{
    public function ask(string $message, array $conversationHistory = [], string $systemPrompt = ''): ?string
    {
        $apiKey = config('services.groq.key');
        if (!$apiKey) return null;

        try {
            $messages = [];

            // System prompt
            if ($systemPrompt) {
                $messages[] = ['role' => 'system', 'content' => $systemPrompt];
            }

            // Conversation history (last 10 turns for context window)
            foreach (array_slice($conversationHistory, -20) as $msg) {
                $messages[] = [
                    'role'    => $msg['role'] === 'user' ? 'user' : 'assistant',
                    'content' => $msg['content'],
                ];
            }

            // Current message
            $messages[] = ['role' => 'user', 'content' => $message];

            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => config('services.groq.model', 'llama-3.3-70b-versatile'),
                    'messages'    => $messages,
                    'temperature' => 0.7,
                    'max_tokens'  => 1024,
                    'top_p'       => 0.9,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? null;
            }

            // Log the error for debugging
            \Log::warning('Groq API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw $e; // Propagate — lets ChatbotService detect offline
        } catch (\Throwable $e) {
            \Log::error('Groq provider error: ' . $e->getMessage());
        }

        return null;
    }

    public function name(): string
    {
        return 'Groq AI';
    }

    public function isAvailable(): bool
    {
        return !empty(config('services.groq.key'));
    }
}
