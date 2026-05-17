<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;

class DuckDuckGoProvider implements AiProviderInterface
{
    public function ask(string $message, array $conversationHistory = [], string $systemPrompt = ''): ?string
    {
        try {
            $response = Http::timeout(8)->get('https://api.duckduckgo.com/', [
                'q'              => $message,
                'format'         => 'json',
                'no_html'        => 1,
                'skip_disambig'  => 1,
            ]);

            if (!$response->successful()) return null;

            $data = $response->json();

            // Try AbstractText first (best quality)
            if (!empty($data['AbstractText'])) {
                $source = $data['AbstractSource'] ?? 'DuckDuckGo';
                $url = $data['AbstractURL'] ?? '';
                $heading = $data['Heading'] ?? '';

                $result = '';
                if ($heading) $result .= "**{$heading}**\n\n";
                $result .= $data['AbstractText'];
                if ($url) $result .= "\n\n🔗 [Source: {$source}]({$url})";

                return $result;
            }

            // Try Answer (for calculations, conversions, etc.)
            if (!empty($data['Answer'])) {
                return "**Answer:** " . strip_tags($data['Answer']);
            }

            // Try Definition
            if (!empty($data['Definition'])) {
                $source = $data['DefinitionSource'] ?? '';
                $result = $data['Definition'];
                if ($source) $result .= "\n\n📖 *Source: {$source}*";
                return $result;
            }

            // Try RelatedTopics (brief summaries)
            if (!empty($data['RelatedTopics'])) {
                $topics = [];
                foreach (array_slice($data['RelatedTopics'], 0, 3) as $topic) {
                    if (isset($topic['Text'])) {
                        $topics[] = $topic['Text'];
                    }
                }
                if (!empty($topics)) {
                    $heading = $data['Heading'] ?? 'Related Information';
                    return "**{$heading}**\n\n" . implode("\n\n", $topics);
                }
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('DuckDuckGo provider error: ' . $e->getMessage());
        }

        return null;
    }

    public function name(): string
    {
        return 'DuckDuckGo';
    }

    public function isAvailable(): bool
    {
        return true; // No API key needed
    }
}
