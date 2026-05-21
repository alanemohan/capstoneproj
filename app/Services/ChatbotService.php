<?php

namespace App\Services;

use App\Models\ChatbotLog;
use App\Models\ChatbotQA;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\User;
use App\Services\AI\AiProviderInterface;
use App\Services\AI\GroqProvider;
use App\Services\AI\GeminiProvider;
use App\Services\AI\WikipediaProvider;
use App\Services\AI\DuckDuckGoProvider;
use App\Services\AI\OllamaProvider;
use App\Services\AI\VectorSearchService;
use Illuminate\Support\Facades\Auth;

class ChatbotService
{
    private VectorSearchService $vectorSearch;
    private \App\Services\NLP\NlpService $nlp;

    /** AI providers in priority order */
    private array $onlineProviders = [];
    private array $offlineProviders = [];

    public function __construct()
    {
        $this->vectorSearch = new VectorSearchService();
        $this->nlp = new \App\Services\NLP\NlpService();

        // Online providers: tried in order, first success wins
        $this->onlineProviders = [
            new GeminiProvider(),
            new GroqProvider(),
            new OllamaProvider(),
            new WikipediaProvider(),
            new DuckDuckGoProvider(),
        ];

        // Offline providers: used when internet is down
        $this->offlineProviders = [
            new OllamaProvider(),  // Local LLM — works without internet
        ];
    }

    /**
     * Main entry point — respond to a user message.
     *
     * @param  string       $message         The user's message
     * @param  int|null     $userId          The user ID (for context/history)
     * @param  string|null  $conversationId  Groups messages into conversations
     * @return array{response: string, intent: string, subject: ?string, confidence: float, source: string}
     */
    public function respond(string $message, ?int $userId = null, ?string $conversationId = null): array
    {
        $userId = $userId ?? Auth::id();
        $raw = trim($message);

        // ── 1. NLP Intent & Entity Detection ─────────────────────────────────
        $nlpAnalysis = $this->nlp->detectIntent($raw, $userId);
        $intent = $nlpAnalysis['intent'];
        $entities = $nlpAnalysis['entities'];
        $lang = $nlpAnalysis['language'];

        // ── 2. Smart LMS intent execution (fast-path) ────────────────────────
        $lmsResult = $this->executeLmsIntent($intent, $entities, $lang, $userId, $raw);
        if ($lmsResult) return $lmsResult;

        // ── 3. Math solver (instant, no AI needed) ───────────────────────────
        $mathResult = $this->solveMath($raw);
        if ($mathResult) return $mathResult;

        // ── 4. Build conversation context ────────────────────────────────────
        $conversationHistory = $this->loadConversationHistory($userId, $conversationId);
        $systemPrompt = $this->buildSystemPrompt($userId);

        // Append specific multilingual instruction for the AI model
        if ($lang === 'hi') {
            $systemPrompt .= "\n\n⚠️ CRITICAL: The user has asked in Hindi. You MUST respond in Hindi (using Devanagari script). Keep the tone academic, warm, and clear.";
        } elseif ($lang === 'pa') {
            $systemPrompt .= "\n\n⚠️ CRITICAL: The user has asked in Punjabi. You MUST respond in Punjabi (using Gurmukhi script). Keep the tone academic, warm, and clear.";
        }

        // ── 5. RAG: Retrieve relevant knowledge for the AI context ───────────
        $ragContext = $this->vectorSearch->getContextForPrompt($raw);
        if ($ragContext) {
            $systemPrompt .= "\n\n" . $ragContext;
        }

        // ── 6. Try online AI providers ───────────────────────────────────────
        $isOffline = false;
        foreach ($this->onlineProviders as $provider) {
            if (!$provider->isAvailable()) continue;

            try {
                $response = $provider->ask($raw, $conversationHistory, $systemPrompt);
                if ($response) {
                    return [
                        'response'   => $response,
                        'intent'     => $intent,
                        'subject'    => $entities['subject'] ?? null,
                        'confidence' => 0.95,
                        'source'     => $provider->name(),
                    ];
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $isOffline = true;
                break; // No internet — switch to offline mode
            }
        }

        // ── 7. Offline mode ──────────────────────────────────────────────────
        // 7a. Try local LLM (Ollama) if available
        foreach ($this->offlineProviders as $provider) {
            if (!$provider->isAvailable()) continue;

            try {
                $response = $provider->ask($raw, $conversationHistory, $systemPrompt);
                if ($response) {
                    return [
                        'response'   => $response,
                        'intent'     => $intent,
                        'subject'    => $entities['subject'] ?? null,
                        'confidence' => 0.85,
                        'source'     => $provider->name(),
                    ];
                }
            } catch (\Throwable $e) {
                // Local model also failed, continue to knowledge base
            }
        }

        // 7b. RAG vector search — find best matching knowledge document
        $ragResults = $this->vectorSearch->search($raw, 1, 0.15);
        if ($ragResults->isNotEmpty()) {
            $best = $ragResults->first();
            $doc = $best['document'];
            return [
                'response'   => "📚 **{$doc->title}**\n\n{$doc->content}",
                'intent'     => 'knowledge_base',
                'subject'    => $doc->category,
                'confidence' => min($best['score'] + 0.3, 0.95),
                'source'     => 'Knowledge Base',
            ];
        }

        // 7c. DB Q&A search
        $dbResult = $this->searchDatabaseQA($raw);
        if ($dbResult) return $dbResult;

        // ── 8. Final fallback ────────────────────────────────────────────────
        $offlineNote = $isOffline
            ? "🔌 **I'm currently offline.** I can still help with LMS features and basic academic topics.\n\n"
            : '';

        if ($lang === 'hi') {
            return [
                'response'   => $offlineNote . "मुझे आपके प्रश्न का सटीक उत्तर नहीं मिल सका।\n\n💡 **आप पूछ सकते हैं:**\n• \"पॉलीमॉर्फिज्म क्या है?\"\n• \"मेरे लंबित असाइनमेंट दिखाओ\"\n• \"गणित के पाठ्यक्रम खोजें\"",
                'intent'     => 'unknown',
                'subject'    => null,
                'confidence' => 0.0,
                'source'     => 'fallback',
            ];
        } elseif ($lang === 'pa') {
            return [
                'response'   => $offlineNote . "ਮੈਨੂੰ ਤੁਹਾਡੇ ਸਵਾਲ ਦਾ ਸਹੀ ਜਵਾਬ ਨਹੀਂ ਮਿਲ ਸਕਿਆ।\n\n💡 **ਤੁਸੀਂ ਪੁੱਛ ਸਕਦੇ ਹੋ:**\n• \"ਪੌਲੀਮੋਰਫਿਜ਼ਮ ਕੀ ਹੁੰਦਾ ਹੈ?\"\n• \"ਮੇਰੇ ਪੈਂਡਿੰਗ ਅਸਾਈਨਮੈਂਟ ਦਿਖਾਓ\"\n• \"ਗਣਿਤ ਦੇ ਕੋਰਸ ਲੱਭੋ\"",
                'intent'     => 'unknown',
                'subject'    => null,
                'confidence' => 0.0,
                'source'     => 'fallback',
            ];
        }

        return [
            'response'   => $offlineNote . "I couldn't find a specific answer for your question.\n\n💡 **Try asking:**\n• \"How do I upload a course?\"\n• \"Show my enrolled courses\"\n• \"Explain polymorphism in Java\"\n• \"What is photosynthesis?\"\n\nOr type **help** for all available topics.",
            'intent'     => 'unknown',
            'subject'    => null,
            'confidence' => 0.0,
            'source'     => 'fallback',
        ];
    }

    // ─── LMS Intent Detection (Fast-Path) ────────────────────────────────────

    private function executeLmsIntent(string $intent, array $entities, string $lang, ?int $userId, string $raw): ?array
    {
        $user = $userId ? User::find($userId) : Auth::user();
        if (!$user) return null;

        $lower = strtolower($raw);

        // Multilingual localizations
        $res = [
            'en' => [
                'no_courses' => "You are not enrolled in any courses matching your request.",
                'courses_found' => "📚 We found these matching courses for you:\n\n",
                'no_assignments' => "You have no pending assignments matching your request.",
                'assignments_found' => "📝 Here are your pending assignments:\n\n",
                'no_quizzes' => "No active quizzes matching your request are available.",
                'quizzes_found' => "✍️ Here are the quizzes available for you:\n\n"
            ],
            'hi' => [
                'no_courses' => "आपके अनुरोध से मेल खाने वाले किसी भी पाठ्यक्रम में आप नामांकित नहीं हैं।",
                'courses_found' => "📚 हमें आपके लिए ये प्रासंगिक पाठ्यक्रम मिले हैं:\n\n",
                'no_assignments' => "आपके पास कोई लंबित असाइनमेंट नहीं है।",
                'assignments_found' => "📝 यहाँ आपके लंबित असाइनमेंट हैं:\n\n",
                'no_quizzes' => "आपके अनुरोध से मेल खाने वाली कोई सक्रिय प्रश्नोत्तरी उपलब्ध नहीं है।",
                'quizzes_found' => "✍️ यहाँ आपके लिए उपलब्ध क्विज़ हैं:\n\n"
            ],
            'pa' => [
                'no_courses' => "ਤੁਸੀਂ ਆਪਣੀ ਬੇਨਤੀ ਨਾਲ ਮੇਲ ਖਾਂਦੇ ਕਿਸੇ ਵੀ ਕੋਰਸ ਵਿੱਚ ਦਾਖਲ ਨਹੀਂ ਹੋ।",
                'courses_found' => "📚 ਸਾਨੂੰ ਤੁਹਾਡੇ ਲਈ ਇਹ ਢੁਕਵੇਂ ਕੋਰਸ ਮਿਲੇ ਹਨ:\n\n",
                'no_assignments' => "ਤੁਹਾਡੇ ਕੋਲ ਕੋਈ ਪੈਂਡਿੰਗ ਅਸਾਈਨਮੈਂਟ ਨਹੀਂ ਹੈ।",
                'assignments_found' => "📝 ਇੱਥੇ ਤੁਹਾਡੇ ਪੈਂਡਿੰਗ ਅਸਾਈਨਮੈਂਟ ਹਨ:\n\n",
                'no_quizzes' => "ਤੁਹਾਡੀ ਬੇਨਤੀ ਨਾਲ ਮੇਲ ਖਾਂਦਾ ਕੋਈ ਸਰਗਰਮ ਕੁਇਜ਼ ਉਪਲਬਧ ਨਹੀਂ ਹੈ।",
                'quizzes_found' => "✍️ ਇੱਥੇ ਤੁਹਾਡੇ ਲਈ ਉਪਲਬਧ ਕੁਇਜ਼ ਹਨ:\n\n"
            ]
        ];

        $langRes = $res[$lang] ?? $res['en'];

        // Greeting Intent
        if ($intent === 'greeting') {
            $name = $user ? " " . $user->name : '';
            if ($lang === 'hi') {
                return $this->lmsResponse("नमस्ते{$name}! 🙏 मैं आपका **एआई चैटबॉट** हूं।\n\nमैं आपकी मदद कर सकता हूं:\n• 🌍 विज्ञान, गणित, कोडिंग जैसे किसी भी विषय पर प्रश्न पूछें\n• 📚 पाठ्यक्रम प्रबंधन और पाठ अपलोड\n• 📝 लंबित असाइनमेंट और क्विज़ खोजें\n• 📊 अपनी पढ़ाई की प्रगति देखें", 'greeting', 'lms');
            } elseif ($lang === 'pa') {
                return $this->lmsResponse("ਸਤਿ ਸ੍ਰੀ ਅਕਾਲ{$name}! 🙏 ਮੈਂ ਤੁਹਾਡਾ **ਏਆਈ ਚੈਟਬਾਟ** ਹਾਂ।\n\nਮੈਂ ਤੁਹਾਡੀ ਮਦਦ ਕਰ ਸਕਦਾ ਹਾਂ:\n• 🌍 ਵਿਗਿਆਨ, ਗਣਿਤ, ਕੋਡਿੰਗ ਵਰਗੇ ਕਿਸੇ ਵੀ ਵਿਸ਼ੇ 'ਤੇ ਸਵਾਲ ਪੁੱਛੋ\n• 📚 ਕੋਰਸ ਪ੍ਰਬੰਧਨ ਅਤੇ ਪਾਠ ਅਪਲੋਡ\n• 📝 ਪੈਂਡਿੰਗ ਅਸਾਈਨਮੈਂਟ ਅਤੇ ਕੁਇਜ਼ ਲੱਭੋ\n• 📊 ਆਪਣੀ ਪੜ੍ਹਾਈ ਦੀ ਪ੍ਰਗਤੀ ਦੇਖੋ", 'greeting', 'lms');
            }
            return $this->lmsResponse("Hello{$name}! 🙏 I'm your **AI Chatbot**, powered by advanced NLP.\n\n**I can help you with:**\n• 🌍 **Any academic question** — science, math, history, coding\n• 📚 Course uploads & management\n• 📝 Assignments & quizzes\n• 🔑 Login & password issues\n• 📊 Enrollment & progress", 'greeting', 'lms');
        }

        // Help Intent
        if ($intent === 'help') {
            if ($lang === 'hi') {
                return $this->lmsResponse("यहाँ कुछ चीजें दी गई हैं जो आप मुझसे पूछ सकते हैं:\n\n• \"मेरे नामांकित पाठ्यक्रम दिखाओ\"\n• \"मेरे पास कितने असाइनमेंट बचे हैं?\"\n• \"कक्षा 9 के गणित के पाठ्यक्रम खोजें\"\n• \"पॉलीमॉर्फिज्म क्या है?\"\n• \"पासवर्ड कैसे रीसेट करें?\"", 'help', 'lms');
            } elseif ($lang === 'pa') {
                return $this->lmsResponse("ਇੱਥੇ ਕੁਝ ਚੀਜ਼ਾਂ ਹਨ ਜੋ ਤੁਸੀਂ ਮੈਨੂੰ ਪੁੱਛ ਸਕਦੇ ਹੋ:\n\n• \"ਮੇਰੇ ਦਾਖਲ ਕੀਤੇ ਕੋਰਸ ਦਿਖਾਓ\"\n• \"ਮੇਰੇ ਕੋਲ ਕਿੰਨੇ ਅਸਾਈਨਮੈਂਟ ਬਾਕੀ ਹਨ?\"\n• \"ਕਲਾਸ 9 ਦੇ ਗਣਿਤ ਦੇ ਕੋਰਸ ਲੱਭੋ\"\n• \"ਪੌਲੀਮੋਰਫਿਜ਼ਮ ਕੀ ਹੁੰਦਾ ਹੈ?\"\n• \"ਪਾਸਵਰਡ ਕਿਵੇਂ ਰੀਸੈਟ ਕਰੀਏ?\"", 'help', 'lms');
            }
            return $this->lmsResponse("I'm an **AI-powered assistant** that can help with:\n\n**🌍 General Knowledge:**\n• Any question — science, history, geography, current affairs\n• Programming & computer science\n• Mathematics & problem solving\n\n**📚 LMS Features:**\n• Upload course / add lesson\n• My enrolled courses\n• Assignment status\n• Password reset / login help\n• Teacher approval status\n\n*Just type your question naturally!*", 'help', 'lms');
        }

        // Query Enrolled Courses Intent
        if ($intent === 'query_courses') {
            if ($user->isStudent()) {
                $query = Course::published()->whereHas('enrollments', fn($q) => $q->where('user_id', $user->id));
                if (!empty($entities['subject'])) {
                    $query->where('subject', $entities['subject']);
                }
                if (!empty($entities['class_level'])) {
                    $query->where('class_level', $entities['class_level']);
                }
                $courses = $query->get();

                if ($courses->isEmpty()) {
                    return $this->lmsResponse($langRes['no_courses'] . " 👉 Visit the **Courses** section in the sidebar to enroll!", 'query_courses', 'lms');
                }

                $list = $courses->map(fn($c) => "• **{$c->title}** (" . ($c->subject ?: 'General') . " - {$c->class_level})")->join("\n");
                return $this->lmsResponse($langRes['courses_found'] . $list . "\n\n👉 Go to **My Courses** in the sidebar to continue learning!", 'query_courses', 'lms');
            }

            if ($user->isTeacher()) {
                $count = Course::where('teacher_id', $user->id)->count();
                return $this->lmsResponse("📚 You have created **{$count} course(s)** on the platform.\n\n👉 Go to **My Courses** in the sidebar.", 'query_courses', 'lms');
            }
        }

        // Query Assignments Intent
        if ($intent === 'query_assignments') {
            if ($user->isStudent()) {
                $query = Assignment::whereDoesntHave('submissions', fn($q) => $q->where('student_id', $user->id))
                    ->where('due_date', '>=', now());
                
                $assignments = $query->take(5)->get();
                if ($assignments->isEmpty()) {
                    return $this->lmsResponse($langRes['no_assignments'], 'query_assignments', 'lms');
                }

                $list = $assignments->map(fn($a) => "• **{$a->title}** (Due: " . ($a->due_date ? $a->due_date->format('Y-m-d') : 'N/A') . ")")->join("\n");
                return $this->lmsResponse($langRes['assignments_found'] . $list . "\n\n👉 Go to **Assignments** in the sidebar to submit them!", 'query_assignments', 'lms');
            }
        }

        // Query Quizzes Intent
        if ($intent === 'query_quizzes') {
            if ($user->isStudent()) {
                $query = Quiz::where('status', 'active');
                if (!empty($entities['subject'])) {
                    $query->where('subject', $entities['subject']);
                }
                $quizzes = $query->take(5)->get();
                
                if ($quizzes->isEmpty()) {
                    return $this->lmsResponse($langRes['no_quizzes'], 'query_quizzes', 'lms');
                }

                $list = $quizzes->map(fn($q) => "• **{$q->title}** (" . ($q->subject ?: 'General') . ")")->join("\n");
                return $this->lmsResponse($langRes['quizzes_found'] . $list . "\n\n👉 Go to **Quizzes** in the sidebar to start a quiz!", 'query_quizzes', 'lms');
            }
        }

        // Query Insights Intent (Dashboard NLP summaries)
        if ($intent === 'query_insights') {
            if ($user->isStudent()) {
                $analytics = $this->nlp->generateStudentAnalytics($user);
                return $this->lmsResponse("📊 **NLP Student Learning Insights:**\n\n" . $analytics['insights'], 'query_insights', 'lms');
            }
            if ($user->isTeacher()) {
                $analytics = $this->nlp->generateTeacherAnalytics($user);
                return $this->lmsResponse("📊 **NLP Teacher Insights:**\n\n" . $analytics['insights_summary'], 'query_insights', 'lms');
            }
            if ($user->isAdmin()) {
                $analytics = $this->nlp->generateAdminComplaintInsights();
                return $this->lmsResponse("📊 **NLP Admin Portal Insights:**\n\n" . $analytics['insights_summary'], 'query_insights', 'lms');
            }
        }

        // My Mentor check
        if (preg_match('/\b(my|who).*(mentor)/i', $lower)) {
            if ($user->mentor_id) {
                $mentor = User::find($user->mentor_id);
                $mentorName = $mentor ? $mentor->name : 'Unknown';
                return $this->lmsResponse("👨‍🏫 Your assigned mentor is **{$mentorName}**.\n\n👉 Visit the **Mentors** section to connect!", 'mentor', 'lms');
            }
            return $this->lmsResponse("You don't have a mentor assigned yet. Contact admin for mentor assignment.", 'mentor', 'lms');
        }

        // Upload course / add lesson check
        if (preg_match('/\b(how|way).*(upload|add|create).*(course|lesson)/i', $lower)) {
            return $this->lmsResponse("📚 **How to Upload a Course:**\n\n1. Login to **Teacher Dashboard**\n2. Click **My Courses** → **Create New Course**\n3. Fill in Title, Subject, Class Level, Language, Description, Price\n4. Upload a **Thumbnail** (max 5MB)\n5. Click **Create Course**\n6. Add lessons with **Add Lesson** button\n7. Submit for admin review\n\n💡 *Supported: PDF, MP4, WebM, MOV, JPG, PNG (max 100MB)*", 'upload_course', 'lms');
        }

        // Password reset check
        if (preg_match('/\b(forgot|reset|change|lost).*(password)/i', $lower)) {
            return $this->lmsResponse("🔑 **Reset Your Password:**\n\n**Via OTP:**\n1. Login page → **Forgot Password / Login with OTP**\n2. Enter phone number → Enter OTP\n3. Go to Profile → Change Password\n\n**Via Profile:**\n1. Profile page → **Change Password**\n2. Enter current + new password\n3. Click **Update Password**", 'forgot_password', 'lms');
        }

        return null;
    }

    private function lmsResponse(string $response, string $intent, string $subject): array
    {
        return [
            'response'   => $response,
            'intent'     => $intent,
            'subject'    => $subject,
            'confidence' => 0.95,
            'source'     => 'LMS System',
        ];
    }

    // ─── Math Solver ─────────────────────────────────────────────────────────

    private function solveMath(string $message): ?array
    {
        $lower = strtolower($message);
        if (preg_match('/(\d+)\s*([\+\-\*\/])\s*(\d+)/', $lower, $matches)) {
            $n1 = (float) $matches[1];
            $op = $matches[2];
            $n2 = (float) $matches[3];

            $res = match($op) {
                '+' => $n1 + $n2,
                '-' => $n1 - $n2,
                '*' => $n1 * $n2,
                '/' => $n2 != 0 ? $n1 / $n2 : 'undefined (division by zero)',
            };

            return [
                'response'   => "🔢 **Math Calculation:**\n\n{$n1} {$op} {$n2} = **{$res}**",
                'intent'     => 'math_solve',
                'subject'    => 'mathematics',
                'confidence' => 1.0,
                'source'     => 'Math Engine',
            ];
        }
        return null;
    }

    // ─── Conversation History ────────────────────────────────────────────────

    private function loadConversationHistory(?int $userId, ?string $conversationId): array
    {
        if (!$userId) return [];

        $query = ChatbotLog::where('user_id', $userId);

        if ($conversationId) {
            $query->where('conversation_id', $conversationId);
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->reverse()
            ->values();

        $history = [];
        foreach ($logs as $log) {
            $history[] = ['role' => 'user', 'content' => $log->message];
            $history[] = ['role' => 'assistant', 'content' => $log->response];
        }

        return $history;
    }

    // ─── System Prompt ───────────────────────────────────────────────────────

    private function buildSystemPrompt(?int $userId): string
    {
        $prompt = <<<PROMPT
You are an advanced AI educational assistant for **Nabha LMS** (Learning Management System), an educational platform based in Punjab, India.

## Your Capabilities:
- Answer ANY question on ANY topic accurately — academics, science, math, programming, history, geography, current affairs, technology, etc.
- Provide clear, concise, student-friendly explanations
- Solve math problems step by step
- Explain programming concepts with code examples
- Help with LMS platform navigation

## Response Guidelines:
- Use **markdown formatting** for clarity (bold, bullet points, code blocks)
- Keep answers concise but thorough (200-500 words typically)
- For code questions, always include properly formatted code blocks with language tags
- For math, show step-by-step solutions
- Be friendly and encouraging — you're helping students learn
- If you truly don't know something, say so honestly
- Answer in the same language the user asks in (English, Hindi, or Punjabi)

## Important Rules:
- NEVER refuse to answer a question — always try your best
- NEVER say you're "just an AI" or make excuses — provide the best answer you can
- For follow-up questions, use conversation context to understand what "it", "he", "she", "this" refers to
- When asked "who is [person]", give a proper biography, not random related topics
PROMPT;

        // Add personalized LMS context for logged-in users
        $user = $userId ? User::find($userId) : null;
        if ($user) {
            $prompt .= "\n\n## Current User Context:";
            $prompt .= "\n- Name: {$user->name}";
            $prompt .= "\n- Role: {$user->role}";
            if ($user->class_level) {
                $prompt .= "\n- Class Level: {$user->class_level}";
            }
            if ($user->isStudent()) {
                // Enrolled courses details
                $enrollments = Enrollment::where('user_id', $user->id)->with('course')->get();
                $enrolledList = $enrollments->map(fn($e) => "  • " . ($e->course?->title ?? 'Untitled Course'))->join("\n");
                
                // Pending assignments details
                $pendingAssignments = Assignment::whereDoesntHave('submissions', fn ($q) => $q->where('student_id', $user->id))
                    ->where('due_date', '>=', now())
                    ->get();
                $assignmentsList = $pendingAssignments->map(fn($a) => "  • {$a->title} (Due: " . ($a->due_date ? $a->due_date->format('Y-m-d') : 'No due date') . ")")->join("\n");

                // Available government schemes
                $schemes = \App\Models\GovernmentScheme::take(3)->get();
                $schemeList = $schemes->map(fn($gs) => "  • {$gs->title}: {$gs->description}")->join("\n");

                // Notifications
                $notifications = $user->portalNotifications()->take(3)->get();
                $notifList = $notifications->map(fn($n) => "  • " . ($n->data['title'] ?? 'LMS Update') . ": " . ($n->data['message'] ?? ''))->join("\n");

                $prompt .= "\n\n### Student Dashboard Context:";
                $prompt .= "\n- Current Streak: {$user->streak_count} day(s)";
                $prompt .= "\n- Completed Lessons Count: " . ($user->lessons_completed ?? 0);
                $prompt .= "\n- Average Quiz Score: " . ($user->total_quiz_score ?? 0) . "%";
                $prompt .= "\n- Enrolled Courses:\n" . ($enrolledList ?: "  None enrolled yet.");
                $prompt .= "\n- Pending Assignments:\n" . ($assignmentsList ?: "  None pending currently.");
                $prompt .= "\n- Active Notifications:\n" . ($notifList ?: "  No new notifications.");
                $prompt .= "\n- Government Schemes Available:\n" . ($schemeList ?: "  None listed.");
            }
            if ($user->isTeacher()) {
                $courses = Course::where('teacher_id', $user->id)->get();
                $courseList = $courses->map(fn($c) => "  • {$c->title} (Status: {$c->status}, Subject: {$c->subject})")->join("\n");

                $studentsCount = Enrollment::whereIn('course_id', $courses->pluck('id'))->distinct('user_id')->count();

                $assignments = Assignment::where('teacher_id', $user->id)->get();
                $assignmentList = $assignments->map(fn($a) => "  • {$a->title} (Due: " . ($a->due_date ? $a->due_date->format('Y-m-d') : 'No due date') . ")")->join("\n");

                $prompt .= "\n\n### Teacher Dashboard Context:";
                $prompt .= "\n- Specialization: {$user->subject_specialization}";
                $prompt .= "\n- Qualification: {$user->qualification}";
                $prompt .= "\n- Total Enrolled Students across your courses: {$studentsCount}";
                $prompt .= "\n- Courses Created:\n" . ($courseList ?: "  No courses created yet.");
                $prompt .= "\n- Assignments Assigned:\n" . ($assignmentList ?: "  No assignments assigned.");
            }
            if ($user->isAdmin()) {
                $totalStudents = User::where('role', 'student')->count();
                $totalTeachers = User::where('role', 'teacher')->count();
                $totalCourses = Course::count();
                $pendingCourses = Course::where('status', 'pending')->count();
                $pendingTeachers = User::where('role', 'teacher')->where('status', 'pending')->count();
                $pendingLessons = Lesson::where('status', 'pending')->count();

                $prompt .= "\n\n### Admin Dashboard Context (Platform Analytics):";
                $prompt .= "\n- Total Platform Students: {$totalStudents}";
                $prompt .= "\n- Total Platform Teachers: {$totalTeachers}";
                $prompt .= "\n- Total Courses Uploaded: {$totalCourses}";
                $prompt .= "\n- Pending Course Approvals: {$pendingCourses}";
                $prompt .= "\n- Pending Teacher Approval Requests: {$pendingTeachers}";
                $prompt .= "\n- Pending Lesson Approvals: {$pendingLessons}";
            }
        }

        return $prompt;
    }

    // ─── Database Q&A Search ─────────────────────────────────────────────────

    private function searchDatabaseQA(string $message): ?array
    {
        $normalized = strtolower(trim($message));

        $qa = ChatbotQA::where('question', 'LIKE', "%{$normalized}%")
            ->orWhere('keywords', 'LIKE', "%{$normalized}%")
            ->first();

        if (!$qa) {
            $words = array_filter(explode(' ', $normalized), fn ($w) => strlen($w) > 3);
            foreach ($words as $word) {
                $qa = ChatbotQA::where('question', 'LIKE', "%{$word}%")
                    ->orWhere('keywords', 'LIKE', "%{$word}%")
                    ->first();
                if ($qa) break;
            }
        }

        if (!$qa) return null;

        return [
            'response'   => $qa->answer,
            'intent'     => 'db_qa',
            'subject'    => null,
            'confidence' => 0.85,
            'source'     => 'Knowledge Base',
        ];
    }
}
