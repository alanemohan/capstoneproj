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

    /** AI providers in priority order */
    private array $onlineProviders = [];
    private array $offlineProviders = [];

    public function __construct()
    {
        $this->vectorSearch = new VectorSearchService();

        // Online providers: tried in order, first success wins
        $this->onlineProviders = [
            new GroqProvider(),
            new GeminiProvider(),
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

        // ── 1. Quick LMS intent detection (fast-path) ────────────────────────
        $lmsResult = $this->detectLmsIntent($raw, $userId);
        if ($lmsResult) return $lmsResult;

        // ── 2. Math solver (instant, no AI needed) ───────────────────────────
        $mathResult = $this->solveMath($raw);
        if ($mathResult) return $mathResult;

        // ── 3. Build conversation context ────────────────────────────────────
        $conversationHistory = $this->loadConversationHistory($userId, $conversationId);
        $systemPrompt = $this->buildSystemPrompt($userId);

        // ── 4. RAG: Retrieve relevant knowledge for the AI context ───────────
        $ragContext = $this->vectorSearch->getContextForPrompt($raw);
        if ($ragContext) {
            $systemPrompt .= "\n\n" . $ragContext;
        }

        // ── 5. Try online AI providers ───────────────────────────────────────
        $isOffline = false;
        foreach ($this->onlineProviders as $provider) {
            if (!$provider->isAvailable()) continue;

            try {
                $response = $provider->ask($raw, $conversationHistory, $systemPrompt);
                if ($response) {
                    return [
                        'response'   => $response,
                        'intent'     => 'ai_response',
                        'subject'    => null,
                        'confidence' => 0.9,
                        'source'     => $provider->name(),
                    ];
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $isOffline = true;
                break; // No internet — switch to offline mode
            }
        }

        // ── 6. Offline mode ──────────────────────────────────────────────────
        // 6a. Try local LLM (Ollama) if available
        foreach ($this->offlineProviders as $provider) {
            if (!$provider->isAvailable()) continue;

            try {
                $response = $provider->ask($raw, $conversationHistory, $systemPrompt);
                if ($response) {
                    return [
                        'response'   => $response,
                        'intent'     => 'ai_response',
                        'subject'    => null,
                        'confidence' => 0.85,
                        'source'     => $provider->name(),
                    ];
                }
            } catch (\Throwable $e) {
                // Local model also failed, continue to knowledge base
            }
        }

        // 6b. RAG vector search — find best matching knowledge document
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

        // 6c. DB Q&A search
        $dbResult = $this->searchDatabaseQA($raw);
        if ($dbResult) return $dbResult;

        // ── 7. Final fallback ────────────────────────────────────────────────
        $offlineNote = $isOffline
            ? "🔌 **I'm currently offline.** I can still help with LMS features and basic academic topics.\n\n"
            : '';

        return [
            'response'   => $offlineNote . "I couldn't find a specific answer for your question.\n\n💡 **Try asking:**\n• \"How do I upload a course?\"\n• \"Show my enrolled courses\"\n• \"Explain polymorphism in Java\"\n• \"What is photosynthesis?\"\n\nOr type **help** for all available topics.",
            'intent'     => 'unknown',
            'subject'    => null,
            'confidence' => 0.0,
            'source'     => 'fallback',
        ];
    }

    // ─── LMS Intent Detection (Fast-Path) ────────────────────────────────────

    private function detectLmsIntent(string $raw, ?int $userId): ?array
    {
        $lower = strtolower($raw);

        // Greetings
        $greetings = ['hello', 'hi', 'hey', 'namaste', 'namaskar', 'good morning', 'good afternoon', 'good evening', 'sat sri akal'];
        foreach ($greetings as $g) {
            if (str_contains($lower, $g)) {
                $user = Auth::user();
                $name = $user ? " {$user->name}" : '';
                return [
                    'response'   => "Namaste{$name}! 🙏 I'm your **AI Study Assistant**, powered by advanced AI.\n\n**I can help you with:**\n• 🌍 **Any question in the world** — science, math, history, coding, current affairs\n• 📚 Course uploads & management\n• 📝 Assignments & quizzes\n• 🔑 Login & password issues\n• 📊 Enrollment & progress\n\nJust ask me anything!",
                    'intent'     => 'greeting',
                    'subject'    => null,
                    'confidence' => 1.0,
                    'source'     => 'system',
                ];
            }
        }

        // Farewells
        $farewells = ['bye', 'goodbye', 'see you', 'alvida', 'thank you', 'thanks', 'dhanyawad'];
        foreach ($farewells as $f) {
            if (str_contains($lower, $f)) {
                return [
                    'response'   => "Goodbye! Keep learning! 📚 Come back anytime. Jai Hind! 🇮🇳",
                    'intent'     => 'farewell',
                    'subject'    => null,
                    'confidence' => 1.0,
                    'source'     => 'system',
                ];
            }
        }

        // Help
        $helpTriggers = ['help', 'what can you do', 'what do you know', 'topics', 'what can you answer'];
        foreach ($helpTriggers as $t) {
            if (str_contains($lower, $t)) {
                return [
                    'response'   => "I'm an **AI-powered assistant** that can help with:\n\n**🌍 General Knowledge:**\n• Any question — science, history, geography, current affairs\n• Programming & computer science\n• Mathematics & problem solving\n\n**📚 LMS Features:**\n• Upload course / add lesson\n• My enrolled courses\n• Assignment status\n• Password reset / login help\n• Teacher approval status\n• Contact admin\n\n**🤖 AI Modes:**\n• 🟢 Online — Uses Groq AI / Gemini for intelligent answers\n• 🟡 Local — Uses Ollama (if installed) for offline AI\n• 📚 Knowledge Base — Built-in educational content\n\n*Just type your question naturally!*",
                    'intent'     => 'help',
                    'subject'    => null,
                    'confidence' => 1.0,
                    'source'     => 'system',
                ];
            }
        }

        // ── Dynamic LMS queries ──────────────────────────────────────────
        $user = $userId ? User::find($userId) : Auth::user();
        if (!$user) return null;

        // "Show my courses" / "my enrolled courses"
        if (preg_match('/\b(my|show|view|list).*(course|enrol|class)/i', $lower)) {
            if ($user->isStudent()) {
                $enrollments = Enrollment::where('user_id', $user->id)->with('course')->get();
                if ($enrollments->isEmpty()) {
                    return $this->lmsResponse("You have **no enrolled courses** yet.\n\n👉 Visit the **Courses** section to browse and enroll!", 'my_courses', 'lms');
                }
                $list = $enrollments->map(fn ($e) => "• **{$e->course->title}**")->join("\n");
                return $this->lmsResponse("📚 You are enrolled in **{$enrollments->count()} course(s)**:\n\n{$list}\n\n👉 Go to **My Courses** in the sidebar to continue learning!", 'my_courses', 'lms');
            }
            if ($user->isTeacher()) {
                $count = Course::where('teacher_id', $user->id)->count();
                return $this->lmsResponse("📚 You have **{$count} course(s)** on the platform.\n\n👉 Go to **My Courses** in the sidebar.", 'my_courses', 'lms');
            }
        }

        // "My assignments" / "pending assignments"
        if (preg_match('/\b(my|show|pending|due|view).*(assignment|homework)/i', $lower)) {
            if ($user->isStudent()) {
                $pendingCount = Assignment::whereDoesntHave('submissions', fn ($q) => $q->where('student_id', $user->id))
                    ->where('due_date', '>=', now())
                    ->count();
                return $this->lmsResponse("📝 You have **{$pendingCount} pending assignment(s)**.\n\n👉 Go to **Assignments** in the sidebar to view and submit them.\n\n⚠️ Check due dates — late submissions may not be accepted!", 'assignments', 'lms');
            }
        }

        // "My mentor" / "who is my mentor"
        if (preg_match('/\b(my|who).*(mentor)/i', $lower)) {
            if ($user->mentor_id) {
                $mentor = User::find($user->mentor_id);
                $mentorName = $mentor ? $mentor->name : 'Unknown';
                return $this->lmsResponse("👨‍🏫 Your assigned mentor is **{$mentorName}**.\n\n👉 Visit the **Mentors** section to connect!", 'mentor', 'lms');
            }
            return $this->lmsResponse("You don't have a mentor assigned yet. Contact admin for mentor assignment.", 'mentor', 'lms');
        }

        // "Teacher approval" / "account pending"
        if (preg_match('/\b(approval|approve|pending|status|rejected).*(teacher|account)?/i', $lower) && $user->isTeacher()) {
            $status = $user->status ?? 'approved';
            $statusMsg = match($status) {
                'pending'  => "⏳ Your teacher account is **PENDING** admin approval.",
                'approved' => "✅ Your teacher account is **APPROVED**! You can create courses.",
                'rejected' => "❌ Your teacher account was **REJECTED**. Please contact admin.",
                default    => "Your status: **{$status}**.",
            };
            return $this->lmsResponse($statusMsg, 'teacher_approval', 'lms');
        }

        // Upload course / add lesson
        if (preg_match('/\b(how|way).*(upload|add|create).*(course|lesson)/i', $lower)) {
            return $this->lmsResponse("📚 **How to Upload a Course:**\n\n1. Login to **Teacher Dashboard**\n2. Click **My Courses** → **Create New Course**\n3. Fill in Title, Subject, Class Level, Language, Description, Price\n4. Upload a **Thumbnail** (max 5MB)\n5. Click **Create Course**\n6. Add lessons with **Add Lesson** button\n7. Submit for admin review\n\n💡 *Supported: PDF, MP4, WebM, MOV, JPG, PNG (max 100MB)*", 'upload_course', 'lms');
        }

        // Password reset
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
                $enrolledCount = Enrollment::where('user_id', $user->id)->count();
                $prompt .= "\n- Enrolled Courses: {$enrolledCount}";
            }
            if ($user->isTeacher()) {
                $courseCount = Course::where('teacher_id', $user->id)->count();
                $prompt .= "\n- Courses Created: {$courseCount}";
                $prompt .= "\n- Status: " . ($user->status ?? 'approved');
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
