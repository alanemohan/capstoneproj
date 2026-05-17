<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;

/**
 * Local LLM provider via Ollama.
 *
 * Requires Ollama to be installed and running locally:
 *   - Install: https://ollama.ai
 *   - Pull a model: `ollama pull llama3.2` or `ollama pull phi3` or `ollama pull gemma2:2b`
 *   - It runs on http://localhost:11434 by default
 *
 * Recommended free local models (smallest to largest):
 *   - gemma2:2b    (1.6 GB) — fast, good quality
 *   - phi3:mini    (2.3 GB) — Microsoft, great for education
 *   - llama3.2     (2.0 GB) — Meta, versatile
 *   - mistral      (4.1 GB) — best quality, needs more RAM
 *   - tinyllama    (637 MB) — ultralight, basic quality
 */
class OllamaProvider implements AiProviderInterface
{
    private string $baseUrl;
    private string $model;

    public function __construct()
    {
        $this->baseUrl = config('services.ollama.url', 'http://localhost:11434');
        $this->model   = config('services.ollama.model', 'llama3.2');
    }

    public function ask(string $message, array $conversationHistory = [], string $systemPrompt = ''): ?string
    {
        if (!$this->isAvailable()) return null;

        try {
            $messages = [];

            // System prompt
            if ($systemPrompt) {
                $messages[] = ['role' => 'system', 'content' => $systemPrompt];
            }

            // Conversation history (limited for local model performance)
            foreach (array_slice($conversationHistory, -10) as $msg) {
                $messages[] = [
                    'role'    => $msg['role'] === 'user' ? 'user' : 'assistant',
                    'content' => $msg['content'],
                ];
            }

            // Current message
            $messages[] = ['role' => 'user', 'content' => $message];

            $response = Http::timeout(30) // Local models can be slow
                ->post("{$this->baseUrl}/api/chat", [
                    'model'    => $this->model,
                    'messages' => $messages,
                    'stream'   => false,
                    'options'  => [
                        'temperature' => 0.7,
                        'num_predict' => 512,
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['message']['content'] ?? null;
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Ollama not running — this is expected, don't throw
            return null;
        } catch (\Throwable $e) {
            \Log::debug('Ollama provider unavailable: ' . $e->getMessage());
        }

        return null;
    }

    public function name(): string
    {
        return 'Local AI (' . $this->model . ')';
    }

    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(2)->get("{$this->baseUrl}/api/tags");
            return $response->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }
}
