<?php

namespace App\Services\NLP;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\Assignment;
use App\Models\ProgressReport;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class NlpService
{
    /**
     * Multilingual Stopwords for English, Hindi, and Punjabi
     */
    private array $stopwords = [
        'en' => ['i', 'me', 'my', 'myself', 'we', 'our', 'ours', 'ourselves', 'you', 'your', 'yours', 'yourself', 'yourselves', 'he', 'him', 'his', 'himself', 'she', 'her', 'hers', 'herself', 'it', 'its', 'itself', 'they', 'them', 'their', 'theirs', 'themselves', 'what', 'which', 'who', 'whom', 'this', 'that', 'these', 'those', 'am', 'is', 'are', 'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'having', 'do', 'does', 'did', 'doing', 'a', 'an', 'the', 'and', 'but', 'if', 'or', 'because', 'as', 'until', 'while', 'of', 'at', 'by', 'for', 'with', 'about', 'against', 'between', 'into', 'through', 'during', 'before', 'after', 'above', 'below', 'to', 'from', 'up', 'down', 'in', 'out', 'on', 'off', 'over', 'under', 'again', 'further', 'then', 'once', 'here', 'there', 'when', 'where', 'why', 'how', 'all', 'any', 'both', 'each', 'few', 'more', 'most', 'other', 'some', 'such', 'no', 'nor', 'not', 'only', 'own', 'same', 'so', 'than', 'too', 'very', 's', 't', 'can', 'will', 'just', 'don', 'should', 'now', 'show', 'find', 'get', 'list', 'please'],
        'hi' => ['का', 'एक', 'में', 'की', 'है', 'यह', 'से', 'का', 'को', 'और', 'की', 'हैं', 'कि', 'था', 'थे', 'थी', 'कर', 'सकते', 'हो', 'रहे', 'करते', 'किया', 'करना', 'करने', 'लिए', 'पर', 'भी', 'ही', 'कुछ', 'तो', 'इस', 'उस', 'जो', 'या', 'तथा', 'अथवा', 'दिखाओ', 'खोजो', 'सूची'],
        'pa' => ['ਦਾ', 'ਇੱਕ', 'ਵਿੱਚ', 'ਦੀ', 'ਹੈ', 'ਇਹ', 'ਤੋਂ', 'ਨੂੰ', 'ਅਤੇ', 'ਹਨ', 'ਕਿ', 'ਸੀ', 'ਸਨ', 'ਕਰ', 'ਸਕਦੇ', 'ਹੋ', 'ਰਹੇ', 'ਕਰਦੇ', 'ਕੀਤਾ', 'ਕਰਨਾ', 'ਕਰਨ', 'ਲਈ', 'ਤੇ', 'ਵੀ', 'ਹੀ', 'ਕੁਝ', 'ਤਾਂ', 'ਇਸ', 'ਉਸ', 'ਜੋ', 'ਜਾਂ', 'ਤਥਾ', 'ਦਿਖਾਓ', 'ਲੱਭੋ']
    ];

    /**
     * Multilingual Sentiment Dictionary (positive/negative keywords)
     */
    private array $sentimentLexicon = [
        'positive' => [
            'good', 'great', 'excellent', 'amazing', 'love', 'helpful', 'useful', 'easy', 'solved', 'thanks', 'thank you', 'awesome', 'best', 'happy', 'satisfied', 'perfect', 'working',
            'अच्छा', 'सुंदर', 'महान', 'शानदार', 'प्यार', 'मददगार', 'उपयोगी', 'आसान', 'हल', 'धन्यवाद', 'खुश', 'संतुष्ट', 'सटीक', 'बढ़िया',
            'ਚੰਗਾ', 'ਵਧੀਆ', 'ਮਹਾਨ', 'ਸ਼ਾਨਦਾਰ', 'ਪਿਆਰ', 'ਮਦਦਗਾਰ', 'ਲਾਭਦਾਇਕ', 'ਸੌਖਾ', 'ਹੱਲ', 'ਧੰਨਵਾਦ', 'ਖੁਸ਼', 'ਸੰਤੁਸ਼ਟ', 'ਸਹੀ'
        ],
        'negative' => [
            'bad', 'poor', 'slow', 'error', 'broken', 'fail', 'failed', 'issue', 'problem', 'difficult', 'hard', 'stuck', 'wrong', 'worst', 'useless', 'not working', 'unable', 'bug', 'crash',
            'खराब', 'धीमा', 'त्रुटि', 'टूटा', 'असफल', 'मुद्दा', 'समस्या', 'कठिन', 'मुश्किल', 'गलत', 'बेकार', 'काम नहीं', 'असमर्थ', 'दिक्कत',
            'ਮਾੜਾ', 'ਧੀਮਾ', 'ਗਲਤੀ', 'ਟੁੱਟਾ', 'ਅਸਫਲ', 'ਮੁੱਦਾ', 'ਸਮੱਸਿਆ', 'ਔਖਾ', 'ਮੁਸ਼ਕਿਲ', 'ਗਲਤ', 'ਬੇਕਾਰ', 'ਕੰਮ ਨਹੀਂ', 'ਅਸਮਰੱਥ', 'ਮੁਸ਼ਕਲ'
        ],
        'urgency' => [
            'urgent', 'immediate', 'emergency', 'asap', 'now', 'critical', 'important', 'serious', 'help', 'cannot login', 'blocked', 'stolen', 'payment failed', 'failed payment',
            'तुलंत', 'जल्दी', 'आपातकालीन', 'गंभीर', 'महत्वपूर्ण', 'मदद', 'लॉगिन नहीं', 'भुगतान विफल',
            'ਤੁਰੰਤ', 'ਜਲਦੀ', 'ਐਮਰਜੈਂਸੀ', 'ਗੰਭੀਰ', 'ਮਹੱਤਵਪੂਰਨ', 'ਮਦਦ', 'ਲੌਗਇਨ ਨਹੀਂ', 'ਭੁਗਤਾਨ ਅਸਫਲ'
        ]
    ];

    /**
     * Detects language automatically from text (EN, HI, PA).
     */
    public function detectLanguage(string $text): string
    {
        // Gurmukhi character range -> Punjabi
        if (preg_match('/[\x{0A00}-\x{0A7F}]/u', $text)) {
            return 'pa';
        }
        // Devanagari character range -> Hindi
        if (preg_match('/[\x{0900}-\x{097F}]/u', $text)) {
            return 'hi';
        }
        return 'en';
    }

    /**
     * Clean and tokenize text
     */
    public function tokenize(string $text, string $lang = 'en'): array
    {
        $text = mb_strtolower($text, 'UTF-8');
        // Remove punctuation
        $text = preg_replace('/[^\w\s\x{0900}-\x{097F}\x{0A00}-\x{0A7F}]/u', ' ', $text);
        $tokens = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        // Filter stopwords
        $langStopwords = $this->stopwords[$lang] ?? $this->stopwords['en'];
        return array_values(array_filter($tokens, fn($t) => !in_array($t, $langStopwords, true)));
    }

    /**
     * Sentiment and Urgency Analysis
     */
    public function analyzeSentimentAndUrgency(string $text): array
    {
        $lang = $this->detectLanguage($text);
        $tokens = $this->tokenize($text, $lang);
        
        $posCount = 0;
        $negCount = 0;
        $urgCount = 0;

        foreach ($tokens as $token) {
            if (in_array($token, $this->sentimentLexicon['positive'], true)) $posCount++;
            if (in_array($token, $this->sentimentLexicon['negative'], true)) $negCount++;
            if (in_array($token, $this->sentimentLexicon['urgency'], true)) $urgCount++;
        }

        // Sentiment classification
        $sentiment = 'neutral';
        if ($posCount > $negCount) $sentiment = 'positive';
        elseif ($negCount > $posCount) $sentiment = 'negative';

        // Urgency level
        $urgency = 'low';
        if ($urgCount >= 2 || $negCount >= 3) $urgency = 'high';
        elseif ($urgCount === 1 || $negCount >= 1) $urgency = 'medium';

        return [
            'sentiment' => $sentiment,
            'urgency' => $urgency,
            'pos_score' => $posCount,
            'neg_score' => $negCount,
            'urg_score' => $urgCount
        ];
    }

    /**
     * Named Entity Recognition (NER) for Educational context
     */
    public function extractEntities(string $text): array
    {
        $entities = [
            'subject' => null,
            'class_level' => null,
            'topic' => null,
            'action' => null
        ];

        $lower = mb_strtolower($text, 'UTF-8');

        // 1. Detect Class Level
        if (preg_match('/\b(class|grade|standard|कक्षा|ਜਮਾਤ)\s*(\d+|ix|x|xi|xii|9|10|11|12)\b/i', $lower, $m)) {
            $num = $m[2];
            if (in_array($num, ['9', 'ix'], true)) $entities['class_level'] = 'Class 9';
            elseif (in_array($num, ['10', 'x'], true)) $entities['class_level'] = 'Class 10';
            elseif (in_array($num, ['11', 'xi'], true)) $entities['class_level'] = 'Class 11';
            elseif (in_array($num, ['12', 'xii'], true)) $entities['class_level'] = 'Class 12';
        } elseif (preg_match('/\b(9th|10th|11th|12th|नौवीं|दसवीं)\b/i', $lower, $m)) {
            $val = $m[1];
            if (str_contains($val, '9') || str_contains($val, 'नौ')) $entities['class_level'] = 'Class 9';
            if (str_contains($val, '10') || str_contains($val, 'दस')) $entities['class_level'] = 'Class 10';
        }

        // 2. Detect Subject
        $subjectMapping = [
            'mathematics' => ['math', 'maths', 'mathematics', 'calculus', 'algebra', 'geometry', 'arithmetic', 'गणित', 'ਗਣਿਤ'],
            'science' => ['science', 'physics', 'chemistry', 'biology', 'photosynthesis', 'gravity', 'mechanics', 'विज्ञान', 'ਵਿਗਿਆਨ'],
            'english' => ['english', 'grammar', 'literature', 'poetry', 'comprehension', 'अंग्रेजी', 'ਅੰਗਰੇਜ਼ੀ'],
            'social_studies' => ['history', 'civics', 'geography', 'economics', 'social', 'इतिहास', 'ਭੂਗੋਲ']
        ];

        foreach ($subjectMapping as $sub => $aliases) {
            foreach ($aliases as $alias) {
                if (preg_match('/\b' . preg_quote($alias, '/') . '\b/iu', $lower)) {
                    $entities['subject'] = ucfirst(str_replace('_', ' ', $sub));
                    break 2;
                }
            }
        }

        // 3. Detect Action
        $actions = [
            'list' => ['show', 'list', 'display', 'view', 'find', 'दिखाओ', 'खोजो', 'ਦਿਖਾਓ', 'ਲੱਭੋ'],
            'explain' => ['explain', 'what is', 'define', 'meaning', 'how does', 'समझो', 'ਦੱਸੋ', 'ਕੀ ਹੈ', 'परिभाषा'],
            'solve' => ['solve', 'calculate', 'compute', 'result of', 'हल', 'ਹੱਲ']
        ];

        foreach ($actions as $act => $triggers) {
            foreach ($triggers as $t) {
                if (str_contains($lower, $t)) {
                    $entities['action'] = $act;
                    break 2;
                }
            }
        }

        return $entities;
    }

    /**
     * Deep Intent Detection (NLP rules + Context)
     */
    public function detectIntent(string $text, ?int $userId = null): array
    {
        $lower = mb_strtolower(trim($text), 'UTF-8');
        $lang = $this->detectLanguage($text);
        
        $entities = $this->extractEntities($text);
        $lastContext = Session::get('nlp_chatbot_context', []);

        $intent = 'academic_query'; // default primary fallback
        $confidence = 0.5;

        // Greetings Check
        if (preg_match('/\b(hello|hi|hey|namaste|sat sri akal|नमस्कार|ਸਤਿ ਸ੍ਰੀ ਅਕਾਲ)\b/i', $lower)) {
            $intent = 'greeting';
            $confidence = 0.95;
        }
        // LMS Specific: Course Query
        elseif (preg_match('/\b(course|class|enroll|study|पाठ्यक्रम|ਕੋਰਸ)\b/i', $lower) && str_contains($lower, 'my') || str_contains($lower, 'show') || str_contains($lower, 'list')) {
            $intent = 'query_courses';
            $confidence = 0.9;
        }
        // LMS Specific: Assignment Query
        elseif (preg_match('/\b(assignment|homework|pending|due|गृहकार्य|ਅਸਾਈਨਮੈਂਟ)\b/i', $lower)) {
            $intent = 'query_assignments';
            $confidence = 0.9;
        }
        // LMS Specific: Quiz Query
        elseif (preg_match('/\b(quiz|test|quizzes|mcq|परीक्षा|ਟੈਸਟ)\b/i', $lower)) {
            $intent = 'query_quizzes';
            $confidence = 0.9;
        }
        // LMS Specific: Help trigger
        elseif (preg_match('/\b(help|support|topics|faq|सहायता|ਮਦਦ)\b/i', $lower)) {
            $intent = 'help';
            $confidence = 0.95;
        }
        // Admin or Teacher Stats trigger
        elseif (preg_match('/\b(stats|analytics|insights|revenue|performance|प्रदर्शन)\b/i', $lower)) {
            $intent = 'query_insights';
            $confidence = 0.85;
        }

        // Contextual continuation resolution
        if ($intent === 'academic_query' && isset($lastContext['intent'])) {
            // If they ask a follow-up, e.g. "explain it" or "give example" or "any more details"
            if (preg_match('/\b(explain it|give example|more|further|उदाहरण|ਉਦਾਹਰਣ)\b/i', $lower)) {
                $intent = $lastContext['intent'];
                $entities = array_merge($lastContext['entities'] ?? [], array_filter($entities));
                $confidence = 0.8;
            }
        }

        // Save current context to session
        Session::put('nlp_chatbot_context', [
            'intent' => $intent,
            'entities' => $entities,
            'timestamp' => now()
        ]);

        return [
            'intent' => $intent,
            'confidence' => $confidence,
            'entities' => $entities,
            'language' => $lang
        ];
    }

    /**
     * Smart NLP Semantic Search helper for Course / Quiz listing
     */
    public function parseSearchQuery(string $query): array
    {
        $entities = $this->extractEntities($query);
        $lang = $this->detectLanguage($query);
        $tokens = $this->tokenize($query, $lang);

        return [
            'subject' => $entities['subject'],
            'class_level' => $entities['class_level'],
            'keywords' => $tokens
        ];
    }

    /**
     * Student Dashboard NLP Analytics: returns weak subjects & personalized course suggestions
     */
    public function generateStudentAnalytics(User $student): array
    {
        return Cache::remember("student_nlp_analytics_{$student->id}", 3600, function() use ($student) {
            $reports = ProgressReport::where('student_id', $student->id)->with('lesson.course')->get();
            $quizzes = Quiz::whereHas('attempts', fn($q) => $q->where('student_id', $student->id))->get();

            // Track performance by subject
            $subjectPerformance = [];
            
            // 1. Analyze completed lessons per subject
            foreach ($reports as $report) {
                if (!$report->lesson || !$report->lesson->subject) continue;
                $subject = $report->lesson->subject;
                if (!isset($subjectPerformance[$subject])) {
                    $subjectPerformance[$subject] = ['lessons' => 0, 'quiz_avg' => null, 'quiz_count' => 0];
                }
                $subjectPerformance[$subject]['lessons']++;
            }

            // 2. Weakest Subject detection and Recommendations
            $weakSubject = null;
            $minLessons = 999;
            foreach ($subjectPerformance as $sub => $data) {
                if ($data['lessons'] < $minLessons) {
                    $minLessons = $data['lessons'];
                    $weakSubject = $sub;
                }
            }

            $recommendations = [];
            if ($weakSubject) {
                $recommendations = Course::published()
                    ->where('subject', $weakSubject)
                    ->whereDoesntHave('enrollments', fn($q) => $q->where('user_id', $student->id))
                    ->take(3)
                    ->get();
            }

            if (empty($recommendations)) {
                $recommendations = Course::published()
                    ->whereDoesntHave('enrollments', fn($q) => $q->where('user_id', $student->id))
                    ->inRandomOrder()
                    ->take(3)
                    ->get();
            }

            $subjectSummary = '';
            if ($weakSubject) {
                $subjectSummary = "Based on our NLP learning pattern analysis, you have spent the least time on **{$weakSubject}**. We suggest focusing on this subject to ensure a balanced progress report!";
            } else {
                $subjectSummary = "Great job maintaining an active learning pace! Keep up the daily streak to continue mastering your lessons.";
            }

            return [
                'weak_subject' => $weakSubject,
                'subject_performance' => $subjectPerformance,
                'insights' => $subjectSummary,
                'recommendations' => $recommendations
            ];
        });
    }

    /**
     * Teacher Dashboard NLP Analytics: generates teaching summaries and insights
     */
    public function generateTeacherAnalytics(User $teacher): array
    {
        return Cache::remember("teacher_nlp_insights_{$teacher->id}", 1800, function() use ($teacher) {
            $courses = Course::where('teacher_id', $teacher->id)->with('lessons')->get();
            $courseIds = $courses->pluck('id');

            $reports = ProgressReport::whereIn('lesson_id', function($q) use ($courseIds) {
                $q->select('id')->from('lessons')->whereIn('course_id', $courseIds);
            })->get();

            $totalReports = $reports->count();
            $completedCount = $reports->where('status', 'completed')->count();
            $completionRate = $totalReports > 0 ? round(($completedCount / $totalReports) * 100, 1) : 0;

            // Generate Insights naturally
            $insights = [];
            if ($completionRate > 80) {
                $insights[] = "Outstanding! The student engagement across your courses is exceptionally high with an average completion rate of **{$completionRate}%**.";
            } elseif ($completionRate > 50) {
                $insights[] = "Stable engagement levels observed. Your lessons have a healthy **{$completionRate}%** completion rate.";
            } else {
                $insights[] = "Engagement notice: The overall lesson completion rate is at **{$completionRate}%**. Adding quick follow-up quizzes or short videos might help boost student completion.";
            }

            if ($courses->where('status', 'pending')->count() > 0) {
                $insights[] = "You have courses currently waiting for Admin approval. These will show immediately on the Student catalog once approved.";
            }

            return [
                'completion_rate' => $completionRate,
                'insights_summary' => implode(' ', $insights),
                'total_engagement' => $totalReports
            ];
        });
    }

    /**
     * Admin Dashboard NLP Analytics: auto-categorizes help tickets & sentiment summaries
     */
    public function generateAdminComplaintInsights(): array
    {
        return Cache::remember('admin_complaint_nlp_insights', 600, function() {
            $complaints = \App\Models\Complaint::latest()->take(30)->get();
            
            $categories = [
                'billing' => 0,
                'technical' => 0,
                'general' => 0
            ];
            $urgencies = [
                'high' => 0,
                'medium' => 0,
                'low' => 0
            ];
            
            foreach ($complaints as $c) {
                $analysis = $this->analyzeSentimentAndUrgency($c->message);
                
                // Categorize based on entities
                $entities = $this->extractEntities($c->message);
                $category = 'general';
                $lowerMsg = strtolower($c->message);
                if (preg_match('/\b(price|money|pay|refund|billing|fee|payment|charge|खरीद|भुगतान)\b/iu', $lowerMsg)) {
                    $category = 'billing';
                } elseif (preg_match('/\b(play|video|pdf|load|error|slow|app|login|password|क्रैश|त्रुटि)\b/iu', $lowerMsg)) {
                    $category = 'technical';
                }

                $categories[$category]++;
                $urgencies[$analysis['urgency']]++;
            }

            $summary = "We automatically analyzed your latest " . $complaints->count() . " support complaints. " .
                "**{$urgencies['high']}** tickets require **immediate critical support** attention, " .
                "while **{$categories['technical']}** are technical issue-related and **{$categories['billing']}** relate to billing/purchases.";

            return [
                'categories' => $categories,
                'urgencies' => $urgencies,
                'insights_summary' => $summary
            ];
        });
    }
}
