@extends('layouts.student')
@section('title', 'AI Study Assistant - Nabha Learning')

@section('student-content')
<div class="flex h-[calc(100vh-160px)] min-h-[500px] max-w-5xl mx-auto gap-4">
    {{-- Sidebar: Conversations --}}
    <div id="conv-sidebar" class="hidden md:flex flex-col w-64 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex-shrink-0">
        <div class="p-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-sm text-gray-700">💬 Chats</h3>
            <button onclick="startNewChat()" class="text-xs bg-indigo-600 text-white px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition font-medium">+ New Chat</button>
        </div>
        <div id="conv-list" class="flex-1 overflow-y-auto p-2 space-y-1">
            <div class="text-xs text-gray-400 p-3 text-center">Loading conversations...</div>
        </div>
    </div>

    {{-- Main Chat Area --}}
    <div class="flex-1 flex flex-col bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-700 text-white p-4 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-lg">🤖</div>
                <div class="flex-1">
                    <h2 class="font-bold text-base">AI Study Assistant</h2>
                    <div class="flex items-center gap-2 text-xs text-indigo-200">
                        <span id="status-dot" class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        <span id="status-text">Connecting...</span>
                    </div>
                </div>
                <button onclick="startNewChat()" class="md:hidden text-xs bg-white/20 px-3 py-1.5 rounded-lg hover:bg-white/30 transition">+ New</button>
                <button onclick="toggleDarkMode()" id="dark-toggle" class="p-2 rounded-lg hover:bg-white/10 transition text-lg">🌙</button>
            </div>
        </div>

        {{-- Messages --}}
        <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-sm flex-shrink-0">🤖</div>
                <div class="bg-white border border-gray-200 rounded-2xl rounded-tl-sm px-4 py-3 max-w-lg shadow-sm">
                    <div class="text-sm text-gray-700">Namaste! 👋 I'm your <strong>AI Study Assistant</strong>. I can answer <strong>any question</strong> — academics, coding, general knowledge, and LMS help. Just ask!</div>
                    <p class="text-xs text-gray-400 mt-2">Just now</p>
                </div>
            </div>
            @foreach($recentChats as $log)
            <div class="flex items-end justify-end gap-3">
                <div class="bg-indigo-600 text-white rounded-2xl rounded-br-sm px-4 py-3 max-w-lg shadow-sm">
                    <p class="text-sm">{{ $log->message }}</p>
                    <p class="text-xs text-indigo-300 mt-1 text-right">{{ $log->created_at->format('h:i A') }}</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-sm flex-shrink-0">🤖</div>
                <div class="bg-white border border-gray-200 rounded-2xl rounded-tl-sm px-4 py-3 max-w-lg shadow-sm">
                    <div class="text-sm text-gray-700 ai-response">{!! $log->response !!}</div>
                    @if($log->source)<span class="inline-block mt-1.5 text-xs bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded-full">{{ $log->source }}</span>@endif
                    <p class="text-xs text-gray-400 mt-1">{{ $log->created_at->format('h:i A') }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Typing Indicator --}}
        <div id="typing-indicator" class="hidden px-4 py-2 bg-gray-50 border-t border-gray-100">
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <div class="flex gap-1"><span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay:0ms"></span><span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay:150ms"></span><span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay:300ms"></span></div>
                <span>AI is thinking...</span>
            </div>
        </div>

        {{-- Quick Suggestions --}}
        <div id="suggestions" class="px-4 py-2 border-t border-gray-100 bg-white flex gap-2 overflow-x-auto flex-shrink-0">
            @foreach(['Who is Albert Einstein?', 'Explain polymorphism', 'My enrolled courses', 'What is photosynthesis?', 'Write Python hello world'] as $s)
            <button onclick="sendQuick('{{ $s }}')" class="text-xs bg-gray-100 text-gray-600 px-3 py-1.5 rounded-full hover:bg-indigo-50 hover:text-indigo-700 transition font-medium whitespace-nowrap flex-shrink-0">{{ $s }}</button>
            @endforeach
        </div>

        {{-- Input --}}
        <div class="p-4 border-t border-gray-200 bg-white flex-shrink-0">
            <form id="chat-form" class="flex gap-3">
                @csrf
                <input type="text" id="chat-input" placeholder="Ask me anything..." class="flex-1 px-4 py-3 bg-gray-100 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" maxlength="2000" autocomplete="off">
                <button type="submit" id="send-btn" class="px-5 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium text-sm disabled:opacity-40" disabled>Send</button>
            </form>
        </div>
    </div>
</div>

{{-- Marked.js + Highlight.js CDN --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css">
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

@push('scripts')
<script>
let conversationId = '{{ $conversationId }}';
const chatMessages = document.getElementById('chat-messages');
const chatForm = document.getElementById('chat-form');
const chatInput = document.getElementById('chat-input');
const typingIndicator = document.getElementById('typing-indicator');
const sendBtn = document.getElementById('send-btn');
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Enable send button when input has text
chatInput.addEventListener('input', () => { sendBtn.disabled = !chatInput.value.trim(); });

// Configure marked
marked.setOptions({ breaks: true, gfm: true, highlight: (code, lang) => { try { return lang ? hljs.highlight(code, {language: lang}).value : hljs.highlightAuto(code).value; } catch(e) { return code; } } });

// Scroll to bottom
function scrollBottom() { chatMessages.scrollTop = chatMessages.scrollHeight; }
scrollBottom();

// Render markdown safely
function renderMd(text) {
    if (!text) return '';
    try { return marked.parse(text); } catch(e) { return text.replace(/\n/g, '<br>'); }
}

// Source badge color
function sourceBadge(source) {
    if (!source || source === 'system' || source === 'fallback') return '';
    const colors = { 'Groq AI': 'bg-emerald-100 text-emerald-700', 'Gemini AI': 'bg-blue-100 text-blue-700', 'Wikipedia': 'bg-amber-100 text-amber-700', 'DuckDuckGo': 'bg-orange-100 text-orange-700', 'Knowledge Base': 'bg-purple-100 text-purple-700', 'LMS System': 'bg-indigo-100 text-indigo-700', 'Math Engine': 'bg-pink-100 text-pink-700' };
    const cls = colors[source] || 'bg-gray-100 text-gray-600';
    const icons = { 'Groq AI': '🤖', 'Gemini AI': '🔮', 'Wikipedia': '📖', 'DuckDuckGo': '🦆', 'Knowledge Base': '📚', 'LMS System': '🎓', 'Math Engine': '🔢' };
    const icon = icons[source] || '💡';
    return `<span class="inline-block mt-1.5 text-xs ${cls} px-2 py-0.5 rounded-full">${icon} ${source}</span>`;
}

// Append message
function appendMsg(content, isUser, source) {
    const time = new Date().toLocaleTimeString('en', {hour:'2-digit', minute:'2-digit'});
    const div = document.createElement('div');
    if (isUser) {
        div.className = 'flex items-end justify-end gap-3';
        div.innerHTML = `<div class="bg-indigo-600 text-white rounded-2xl rounded-br-sm px-4 py-3 max-w-lg shadow-sm"><p class="text-sm">${content.replace(/</g,'&lt;')}</p><p class="text-xs text-indigo-300 mt-1 text-right">${time}</p></div>`;
    } else {
        div.className = 'flex items-start gap-3';
        const badge = sourceBadge(source);
        div.innerHTML = `<div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-sm flex-shrink-0">🤖</div><div class="bg-white border border-gray-200 rounded-2xl rounded-tl-sm px-4 py-3 max-w-lg shadow-sm"><div class="text-sm text-gray-700 ai-response prose prose-sm max-w-none">${renderMd(content)}</div>${badge}<p class="text-xs text-gray-400 mt-1">${time}</p><button onclick="copyResponse(this)" class="text-xs text-gray-400 hover:text-indigo-600 mt-1">📋 Copy</button></div>`;
    }
    chatMessages.appendChild(div);
    scrollBottom();
}

function copyResponse(btn) {
    const text = btn.closest('div').querySelector('.ai-response').innerText;
    navigator.clipboard.writeText(text);
    btn.textContent = '✅ Copied!';
    setTimeout(() => btn.textContent = '📋 Copy', 2000);
}

function sendQuick(text) { chatInput.value = text; sendBtn.disabled = false; chatForm.dispatchEvent(new Event('submit')); }

// Send message
chatForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const msg = chatInput.value.trim();
    if (!msg) return;
    appendMsg(msg, true);
    chatInput.value = '';
    sendBtn.disabled = true;
    typingIndicator.classList.remove('hidden');
    scrollBottom();

    try {
        const res = await fetch('{{ route("student.chatbot.chat") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ message: msg, conversation_id: conversationId })
        });
        const data = await res.json();
        typingIndicator.classList.add('hidden');
        if (data.success) {
            appendMsg(data.response, false, data.source);
            if (data.conversation_id) conversationId = data.conversation_id;
        }
    } catch(err) {
        typingIndicator.classList.add('hidden');
        appendMsg('⚠️ Connection error. Please check your internet and try again.', false, 'system');
    }
});

chatInput.addEventListener('keydown', (e) => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); chatForm.dispatchEvent(new Event('submit')); } });

// New Chat
async function startNewChat() {
    try {
        const res = await fetch('{{ route("student.chatbot.new") }}', { method:'POST', headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'} });
        const data = await res.json();
        if (data.success) {
            conversationId = data.conversation_id;
            chatMessages.innerHTML = `<div class="flex items-start gap-3"><div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-sm flex-shrink-0">🤖</div><div class="bg-white border border-gray-200 rounded-2xl rounded-tl-sm px-4 py-3 max-w-lg shadow-sm"><div class="text-sm text-gray-700">New conversation started! 🎉 Ask me anything.</div></div></div>`;
            loadConversations();
        }
    } catch(e) { console.error(e); }
}

// Load conversations sidebar
async function loadConversations() {
    try {
        const res = await fetch('{{ route("student.chatbot.conversations") }}', {headers:{'Accept':'application/json'}});
        const data = await res.json();
        const list = document.getElementById('conv-list');
        if (data.conversations && data.conversations.length) {
            list.innerHTML = data.conversations.map(c => `<button onclick="loadConversation('${c.id}')" class="w-full text-left px-3 py-2 rounded-lg text-xs hover:bg-indigo-50 transition ${c.id===conversationId?'bg-indigo-50 text-indigo-700 font-semibold':'text-gray-600'}"><p class="truncate">${c.title}</p><p class="text-gray-400 mt-0.5">${c.message_count} msgs</p></button>`).join('');
        } else { list.innerHTML = '<div class="text-xs text-gray-400 p-3 text-center">No conversations yet</div>'; }
    } catch(e) {}
}

async function loadConversation(id) {
    conversationId = id;
    try {
        const res = await fetch(`{{ url("student/chatbot/history") }}?conversation_id=${id}`, {headers:{'Accept':'application/json'}});
        const data = await res.json();
        chatMessages.innerHTML = '';
        if (data.history) {
            data.history.forEach(h => { appendMsg(h.message, true); appendMsg(h.response, false, h.source); });
        }
        loadConversations();
    } catch(e) {}
}

// Check connection status
async function checkStatus() {
    try {
        await fetch('https://api.groq.com', {mode:'no-cors',cache:'no-cache'});
        document.getElementById('status-dot').className = 'w-2 h-2 bg-emerald-400 rounded-full animate-pulse';
        document.getElementById('status-text').textContent = 'Online • AI Ready';
    } catch(e) {
        document.getElementById('status-dot').className = 'w-2 h-2 bg-amber-400 rounded-full';
        document.getElementById('status-text').textContent = 'Offline • Local Mode';
    }
}

// Dark mode
function toggleDarkMode() {
    document.querySelector('.bg-gray-50')?.classList.toggle('bg-gray-900');
    document.getElementById('dark-toggle').textContent = document.getElementById('dark-toggle').textContent === '🌙' ? '☀️' : '🌙';
}

// Init
checkStatus(); setInterval(checkStatus, 30000);
loadConversations();
</script>
@endpush
@endsection
