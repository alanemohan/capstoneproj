<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    // MyMemory free API — no key needed, 5000 words/day per IP
    // With email: 50,000 words/day (configurable via MYMEMORY_EMAIL in .env)
    protected string $apiUrl = 'https://api.mymemory.translated.net/get';
    protected ?string $email;
    protected int $cacheTtl = 3600; // 1 hour

    public function __construct()
    {
        $this->email = config('app.mymemory_email', env('MYMEMORY_EMAIL', null));
    }

    /**
     * Translate a single text string.
     *
     * @param string $text    Source text (English)
     * @param string $toLang  Target language code: 'hi' or 'pa'
     * @return string         Translated text, or original on failure
     */
    public function translateText(string $text, string $toLang): string
    {
        if (empty(trim($text))) {
            return $text;
        }

        // Use 'pa' langpair as 'pa-IN' for Punjabi
        $langPairMap = [
            'hi' => 'en|hi',
            'pa' => 'en|pa',
        ];

        $langPair = $langPairMap[$toLang] ?? null;
        if (!$langPair) {
            return $text;
        }

        $cacheKey = 'trans_' . md5($text . $toLang);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($text, $langPair) {
            return $this->callApi($text, $langPair) ?? $text;
        });
    }

    /**
     * Translate multiple texts at once.
     *
     * @param array  $texts   Associative array ['field' => 'English text']
     * @param string $toLang  Target language
     * @return array          Associative array ['field' => 'Translated text']
     */
    public function translateBatch(array $texts, string $toLang): array
    {
        $results = [];
        foreach ($texts as $field => $text) {
            $results[$field] = $this->translateText((string) $text, $toLang);
        }
        return $results;
    }

    /**
     * Translate content for all non-English languages.
     *
     * @param array $fields   ['title' => 'Course Name', 'description' => 'Course description']
     * @return array          ['title_hi' => '...', 'title_pa' => '...', 'description_hi' => '...', 'description_pa' => '...']
     */
    public function translateAllLocales(array $fields): array
    {
        $locales = ['hi', 'pa'];
        $result = [];

        foreach ($locales as $locale) {
            foreach ($fields as $field => $value) {
                if (!empty($value)) {
                    $result["{$field}_{$locale}"] = $this->translateText((string) $value, $locale);
                }
            }
        }

        return $result;
    }

    /**
     * Call the MyMemory translation API.
     */
    protected function callApi(string $text, string $langPair): ?string
    {
        try {
            // Limit text length to avoid API rejections (MyMemory has 500 char limit per request)
            // For longer texts, split and rejoin
            if (mb_strlen($text) > 450) {
                return $this->translateLongText($text, $langPair);
            }

            $params = [
                'q'        => $text,
                'langpair' => $langPair,
            ];

            if ($this->email) {
                $params['de'] = $this->email;
            }

            $response = Http::timeout(10)->get($this->apiUrl, $params);

            if (!$response->ok()) {
                Log::warning('TranslationService: API request failed', [
                    'status'   => $response->status(),
                    'langpair' => $langPair,
                ]);
                return null;
            }

            $data = $response->json();

            if (
                isset($data['responseStatus']) &&
                $data['responseStatus'] == 200 &&
                isset($data['responseData']['translatedText'])
            ) {
                $translated = $data['responseData']['translatedText'];

                // MyMemory sometimes returns MYMEMORY WARNING if limit exceeded
                if (str_contains($translated, 'MYMEMORY WARNING')) {
                    Log::warning('TranslationService: API quota exceeded');
                    return null;
                }

                return $translated;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('TranslationService: Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * For texts > 450 chars: split by sentence, translate each, rejoin.
     */
    protected function translateLongText(string $text, string $langPair): ?string
    {
        // Split by sentence-ending punctuation
        $sentences = preg_split('/(?<=[.!?।])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        if (!$sentences) {
            return null;
        }

        $translated = [];
        $chunk = '';

        foreach ($sentences as $sentence) {
            if (mb_strlen($chunk . ' ' . $sentence) > 450) {
                // Translate current chunk
                $result = $this->callApiChunk($chunk, $langPair);
                if ($result !== null) {
                    $translated[] = $result;
                } else {
                    $translated[] = $chunk; // fallback to original
                }
                $chunk = $sentence;
            } else {
                $chunk = $chunk ? $chunk . ' ' . $sentence : $sentence;
            }
        }

        // Translate remaining chunk
        if ($chunk) {
            $result = $this->callApiChunk($chunk, $langPair);
            $translated[] = $result ?? $chunk;
        }

        return implode(' ', $translated);
    }

    protected function callApiChunk(string $text, string $langPair): ?string
    {
        $params = ['q' => $text, 'langpair' => $langPair];
        if ($this->email) {
            $params['de'] = $this->email;
        }

        try {
            $response = Http::timeout(10)->get($this->apiUrl, $params);
            if ($response->ok()) {
                $data = $response->json();
                if (isset($data['responseData']['translatedText'])) {
                    $t = $data['responseData']['translatedText'];
                    if (!str_contains($t, 'MYMEMORY WARNING')) {
                        return $t;
                    }
                }
            }
        } catch (\Exception $e) {
            // silent
        }

        return null;
    }
}
