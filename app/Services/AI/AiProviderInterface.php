<?php

namespace App\Services\AI;

interface AiProviderInterface
{
    /**
     * Send a message to the AI provider and get a response.
     *
     * @param  string  $message           The user's current message
     * @param  array   $conversationHistory  Previous messages [{role: 'user'|'assistant', content: '...'}]
     * @param  string  $systemPrompt      System instructions for the AI
     * @return string|null                 The AI response text, or null on failure
     */
    public function ask(string $message, array $conversationHistory = [], string $systemPrompt = ''): ?string;

    /**
     * Get the display name of this provider (shown in UI).
     */
    public function name(): string;

    /**
     * Check if this provider is currently available/configured.
     */
    public function isAvailable(): bool;
}
