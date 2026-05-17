<?php

namespace App\Services\AI;

use App\Models\KnowledgeDocument;
use Illuminate\Support\Collection;

/**
 * RAG (Retrieval Augmented Generation) — PHP-native vector search engine.
 *
 * Implements TF-IDF vectorization + cosine similarity for semantic-like
 * document retrieval without requiring Python, FAISS, or external services.
 *
 * Flow:
 *   1. User asks a question
 *   2. VectorSearchService tokenizes the query into a TF-IDF vector
 *   3. Computes cosine similarity against all knowledge document vectors
 *   4. Returns top-N most relevant documents
 *   5. These documents are injected into the AI system prompt as context
 */
class VectorSearchService
{
    /** Stopwords to filter out during tokenization */
    private array $stopWords = [
        'a', 'an', 'the', 'is', 'are', 'am', 'was', 'were', 'be', 'been',
        'being', 'at', 'by', 'for', 'from', 'in', 'of', 'on', 'to', 'with',
        'and', 'but', 'or', 'yet', 'so', 'can', 'could', 'shall', 'should',
        'will', 'would', 'may', 'might', 'must', 'do', 'does', 'did', 'have',
        'has', 'had', 'what', 'which', 'who', 'whom', 'this', 'that', 'these',
        'those', 'it', 'its', 'i', 'me', 'my', 'we', 'us', 'our', 'you',
        'your', 'he', 'him', 'his', 'she', 'her', 'they', 'them', 'their',
        'not', 'no', 'nor', 'if', 'then', 'than', 'how', 'when', 'where',
        'why', 'about', 'into', 'also', 'just', 'more', 'some', 'such',
        'very', 'much', 'many', 'each', 'every', 'all', 'any', 'few',
    ];

    /**
     * Search knowledge documents using TF-IDF cosine similarity.
     *
     * @param  string  $query       The user's question
     * @param  int     $topN        Number of results to return
     * @param  float   $threshold   Minimum similarity score (0-1)
     * @param  string|null $category  Optional category filter
     * @return Collection  Sorted collection of [document, score] pairs
     */
    public function search(string $query, int $topN = 3, float $threshold = 0.1, ?string $category = null): Collection
    {
        $queryTokens = $this->tokenize($query);
        if (empty($queryTokens)) return collect();

        // Build query TF vector
        $queryTf = $this->computeTf($queryTokens);

        // Get all active knowledge documents
        $docsQuery = KnowledgeDocument::active();
        if ($category) {
            $docsQuery->category($category);
        }
        $documents = $docsQuery->get();

        if ($documents->isEmpty()) return collect();

        // Compute IDF across all documents
        $allDocTokens = [];
        foreach ($documents as $doc) {
            $allDocTokens[$doc->id] = $doc->tfidf_vector
                ? array_keys($doc->tfidf_vector)
                : $this->tokenize($doc->content . ' ' . $doc->title . ' ' . ($doc->keywords ?? ''));
        }

        $idf = $this->computeIdf($allDocTokens, $documents->count());

        // Compute query TF-IDF vector
        $queryVector = [];
        foreach ($queryTf as $term => $tf) {
            $queryVector[$term] = $tf * ($idf[$term] ?? log($documents->count() + 1));
        }

        // Score each document
        $scored = [];
        foreach ($documents as $doc) {
            $docVector = $doc->tfidf_vector;

            if (!$docVector) {
                // Compute on-the-fly if not pre-computed
                $docTokens = $this->tokenize($doc->content . ' ' . $doc->title . ' ' . ($doc->keywords ?? ''));
                $docTf = $this->computeTf($docTokens);
                $docVector = [];
                foreach ($docTf as $term => $tf) {
                    $docVector[$term] = $tf * ($idf[$term] ?? log($documents->count() + 1));
                }
            }

            $similarity = $this->cosineSimilarity($queryVector, $docVector);

            if ($similarity >= $threshold) {
                $scored[] = [
                    'document' => $doc,
                    'score'    => round($similarity, 4),
                ];
            }
        }

        // Sort by score descending and take top N
        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

        return collect(array_slice($scored, 0, $topN));
    }

    /**
     * Build and store TF-IDF vectors for all documents.
     * Run this after seeding or when documents change.
     */
    public function reindexAll(): int
    {
        $documents = KnowledgeDocument::active()->get();
        if ($documents->isEmpty()) return 0;

        // Tokenize all documents
        $allDocTokens = [];
        foreach ($documents as $doc) {
            $tokens = $this->tokenize($doc->content . ' ' . $doc->title . ' ' . ($doc->keywords ?? ''));
            $allDocTokens[$doc->id] = $tokens;
        }

        // Compute IDF
        $idf = $this->computeIdf(
            array_map(fn ($tokens) => array_unique($tokens), $allDocTokens),
            $documents->count()
        );

        // Compute and store TF-IDF vector for each document
        $count = 0;
        foreach ($documents as $doc) {
            $tokens = $allDocTokens[$doc->id];
            $tf = $this->computeTf($tokens);

            $vector = [];
            foreach ($tf as $term => $tfVal) {
                $vector[$term] = round($tfVal * ($idf[$term] ?? 0), 6);
            }

            $doc->update(['tfidf_vector' => $vector]);
            $count++;
        }

        return $count;
    }

    /**
     * Format retrieved documents as context for AI system prompt.
     */
    public function getContextForPrompt(string $query, ?string $category = null): string
    {
        $results = $this->search($query, 3, 0.08, $category);

        if ($results->isEmpty()) return '';

        $context = "## Relevant Knowledge Base Context:\n\n";
        foreach ($results as $result) {
            $doc = $result['document'];
            $score = $result['score'];
            $content = \Illuminate\Support\Str::limit($doc->content, 800);
            $context .= "### {$doc->title} (relevance: {$score})\n{$content}\n\n";
        }

        return $context;
    }

    // ─── Internal Methods ────────────────────────────────────────────────────

    /**
     * Tokenize text: lowercase, remove special chars, split, remove stopwords, stem.
     */
    private function tokenize(string $text): array
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        // Remove stopwords and very short words
        $words = array_filter($words, function ($word) {
            return strlen($word) > 2 && !in_array($word, $this->stopWords);
        });

        // Simple stemming: remove common suffixes
        $words = array_map(function ($word) {
            // Very basic Porter-like stemming for English
            $word = preg_replace('/(ing|tion|sion|ment|ness|ous|ive|able|ible|ful|less|ly|ed|er|est|ies)$/', '', $word);
            return $word ?: $word;
        }, $words);

        return array_values(array_filter($words, fn ($w) => strlen($w) > 1));
    }

    /**
     * Compute Term Frequency (TF) for a token list.
     * TF(t) = count(t) / total_tokens
     */
    private function computeTf(array $tokens): array
    {
        $counts = array_count_values($tokens);
        $total = count($tokens);

        $tf = [];
        foreach ($counts as $term => $count) {
            $tf[$term] = $count / $total;
        }

        return $tf;
    }

    /**
     * Compute Inverse Document Frequency (IDF).
     * IDF(t) = log(N / (1 + df(t)))
     */
    private function computeIdf(array $allDocTokens, int $totalDocs): array
    {
        $docFrequency = [];

        foreach ($allDocTokens as $tokens) {
            $uniqueTerms = is_array($tokens) ? array_unique($tokens) : $tokens;
            foreach ($uniqueTerms as $term) {
                $docFrequency[$term] = ($docFrequency[$term] ?? 0) + 1;
            }
        }

        $idf = [];
        foreach ($docFrequency as $term => $df) {
            $idf[$term] = log(($totalDocs + 1) / (1 + $df));
        }

        return $idf;
    }

    /**
     * Compute cosine similarity between two sparse vectors.
     */
    private function cosineSimilarity(array $vecA, array $vecB): float
    {
        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        // Get all unique terms
        $allTerms = array_unique(array_merge(array_keys($vecA), array_keys($vecB)));

        foreach ($allTerms as $term) {
            $a = $vecA[$term] ?? 0.0;
            $b = $vecB[$term] ?? 0.0;
            $dotProduct += $a * $b;
            $normA += $a * $a;
            $normB += $b * $b;
        }

        $denominator = sqrt($normA) * sqrt($normB);

        return $denominator > 0 ? $dotProduct / $denominator : 0.0;
    }
}
