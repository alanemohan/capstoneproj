<?php

namespace App\Http\Controllers;

use App\Models\ChatbotLog;
use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function __construct(private ChatbotService $chatbot) {}

    public function index()
    {
        // Get or create a conversation ID for this session
        $conversationId = session('chatbot_conversation_id', (string) Str::uuid());
        session(['chatbot_conversation_id' => $conversationId]);

        $recentChats = ChatbotLog::where('user_id', auth()->id())
            ->where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->take(50)
            ->get();

        return view('student.chatbot', compact('recentChats', 'conversationId'));
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message'         => ['required', 'string', 'max:2000'],
            'conversation_id' => ['nullable', 'string', 'max:50'],
        ]);

        $message = trim($request->input('message'));
        $conversationId = $request->input('conversation_id')
            ?? session('chatbot_conversation_id', (string) Str::uuid());

        // Store conversation ID in session
        session(['chatbot_conversation_id' => $conversationId]);

        $result = $this->chatbot->respond($message, auth()->id(), $conversationId);

        $log = ChatbotLog::create([
            'user_id'         => auth()->id(),
            'message'         => $message,
            'response'        => $result['response'],
            'intent'          => $result['intent'],
            'subject'         => $result['subject'],
            'confidence'      => $result['confidence'],
            'source'          => $result['source'] ?? null,
            'session_id'      => session()->getId(),
            'conversation_id' => $conversationId,
        ]);

        return response()->json([
            'success'         => true,
            'response'        => $result['response'],
            'intent'          => $result['intent'],
            'subject'         => $result['subject'],
            'confidence'      => $result['confidence'],
            'source'          => $result['source'] ?? 'system',
            'log_id'          => $log->id,
            'conversation_id' => $conversationId,
        ]);
    }

    /**
     * Start a new conversation — resets conversation ID.
     */
    public function newChat()
    {
        $newConversationId = (string) Str::uuid();
        session(['chatbot_conversation_id' => $newConversationId]);

        return response()->json([
            'success'         => true,
            'conversation_id' => $newConversationId,
        ]);
    }

    /**
     * Get chat history for a specific conversation.
     */
    public function history(Request $request)
    {
        $conversationId = $request->input('conversation_id', session('chatbot_conversation_id'));

        $logs = ChatbotLog::where('user_id', auth()->id())
            ->where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->take(50)
            ->get()
            ->map(fn ($log) => [
                'id'       => $log->id,
                'message'  => $log->message,
                'response' => $log->response,
                'source'   => $log->source,
                'time'     => $log->created_at->format('h:i A'),
            ]);

        return response()->json([
            'success' => true,
            'history' => $logs,
        ]);
    }

    /**
     * List all conversations for the current user.
     */
    public function conversations()
    {
        $conversations = ChatbotLog::where('user_id', auth()->id())
            ->whereNotNull('conversation_id')
            ->selectRaw('conversation_id, MIN(message) as first_message, MAX(created_at) as last_active, COUNT(*) as message_count')
            ->groupBy('conversation_id')
            ->orderByDesc('last_active')
            ->take(20)
            ->get()
            ->map(fn ($c) => [
                'id'            => $c->conversation_id,
                'title'         => Str::limit($c->first_message, 40),
                'last_active'   => $c->last_active,
                'message_count' => $c->message_count,
            ]);

        return response()->json([
            'success'       => true,
            'conversations' => $conversations,
        ]);
    }

    public function feedback(Request $request, ChatbotLog $log)
    {
        $request->validate(['helpful' => ['required', 'boolean']]);
        $log->update(['was_helpful' => $request->boolean('helpful')]);
        return response()->json(['success' => true]);
    }

    /**
     * Clear active conversation history or all conversation histories for the logged-in user.
     */
    public function clearHistory(Request $request)
    {
        $conversationId = $request->input('conversation_id');
        $deleteAll = $request->boolean('all', false);

        if ($deleteAll) {
            ChatbotLog::where('user_id', auth()->id())->delete();
            $newConversationId = (string) \Illuminate\Support\Str::uuid();
            session(['chatbot_conversation_id' => $newConversationId]);
            return response()->json([
                'success' => true,
                'conversation_id' => $newConversationId,
                'message' => 'All chat history cleared successfully.'
            ]);
        }

        if ($conversationId) {
            ChatbotLog::where('user_id', auth()->id())
                ->where('conversation_id', $conversationId)
                ->delete();
            
            // If the cleared conversation is the active session one, generate a new one
            if ($conversationId === session('chatbot_conversation_id')) {
                $newConversationId = (string) \Illuminate\Support\Str::uuid();
                session(['chatbot_conversation_id' => $newConversationId]);
            } else {
                $newConversationId = session('chatbot_conversation_id');
            }

            return response()->json([
                'success' => true,
                'conversation_id' => $newConversationId,
                'message' => 'Conversation cleared successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No conversation ID provided.'
        ], 400);
    }
}
