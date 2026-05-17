<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;

class WikipediaProvider implements AiProviderInterface
{
    public function ask(string $message, array $conversationHistory = [], string $systemPrompt = ''): ?string
    {
        try {
            // Step 1: Search Wikipedia for relevant articles
            $searchResponse = Http::timeout(8)
                ->withHeaders(['User-Agent' => 'NabhaLMS/2.0 (Educational Platform)'])
                ->get('https://en.wikipedia.org/w/api.php', [
                    'action'   => 'query',
                    'list'     => 'search',
                    'srsearch' => $message,
                    'utf8'     => 1,
                    'format'   => 'json',
                    'srlimit'  => 3,
                ]);

            if (!$searchResponse->successful()) return null;

            $searchData = $searchResponse->json();
            $results = $searchData['query']['search'] ?? [];

            if (empty($results)) return null;

            // Step 2: Get the full extract of the best matching article
            $title = $results[0]['title'];

            $extractResponse = Http::timeout(8)
                ->withHeaders(['User-Agent' => 'NabhaLMS/2.0 (Educational Platform)'])
                ->get('https://en.wikipedia.org/w/api.php', [
                    'action'      => 'query',
                    'titles'      => $title,
                    'prop'        => 'extracts',
                    'exintro'     => true,
                    'explaintext' => true,
                    'format'      => 'json',
                ]);

            if ($extractResponse->successful()) {
                $pages = $extractResponse->json()['query']['pages'] ?? [];
                $page = reset($pages);

                if (isset($page['extract']) && strlen($page['extract']) > 50) {
                    $extract = $page['extract'];

                    // Trim to reasonable length
                    if (strlen($extract) > 1500) {
                        $extract = substr($extract, 0, 1500);
                        $lastDot = strrpos($extract, '.');
                        if ($lastDot !== false) {
                            $extract = substr($extract, 0, $lastDot + 1);
                        }
                    }

                    $wikiUrl = 'https://en.wikipedia.org/wiki/' . urlencode(str_replace(' ', '_', $title));

                    return "**{$title}**\n\n{$extract}\n\n🔗 [Read more on Wikipedia]({$wikiUrl})";
                }
            }

            // Fallback: use search snippet
            $snippet = strip_tags(html_entity_decode($results[0]['snippet']));
            $wikiUrl = 'https://en.wikipedia.org/wiki/' . urlencode(str_replace(' ', '_', $title));

            return "**{$title}**\n\n{$snippet}...\n\n🔗 [Read more on Wikipedia]({$wikiUrl})";

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('Wikipedia provider error: ' . $e->getMessage());
        }

        return null;
    }

    public function name(): string
    {
        return 'Wikipedia';
    }

    public function isAvailable(): bool
    {
        return true; // Always available — no API key needed
    }
}
