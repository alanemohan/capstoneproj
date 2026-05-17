@extends('layouts.student')
@section('title', 'AI Chatbot - Nabha Learning')

@section('student-content')
<style>
    .chat-msg-enter {
        animation: chatSlideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes chatSlideUp {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(156, 163, 175, 0.15);
        border-radius: 9999px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(156, 163, 175, 0.3);
    }
</style>

<div class="flex h-[calc(100vh-160px)] min-h-[500px] max-w-5xl mx-auto gap-4 animate-fade-in">
    {{-- Sidebar: Conversations --}}
    <div id="conv-sidebar" class="hidden md:flex flex-col w-80 bg-white dark:bg-white/[0.02] rounded-2xl border border-gray-200 dark:border-white/[0.06] backdrop-blur-md overflow-hidden flex-shrink-0 shadow-sm">
        <div class="p-4 border-b border-gray-200 dark:border-white/[0.06] flex items-center justify-between gap-1.5">
            <h3 class="font-bold text-xs text-gray-500 dark:text-white/70 uppercase tracking-wider truncate">💬 History</h3>
            <div class="flex items-center gap-1">
                <button onclick="startNewChat()" class="text-[9px] bg-gradient-to-r from-violet-600 to-indigo-600 text-white px-2.5 py-1.5 rounded-lg hover:from-violet-500 hover:to-indigo-500 hover:shadow-lg hover:shadow-indigo-500/10 transition duration-200 font-bold uppercase tracking-wider">+ New</button>
                <button onclick="confirmClearHistory()" class="text-[9px] bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/10 px-2 py-1.5 rounded-lg hover:bg-rose-600 hover:text-white transition duration-200 font-bold uppercase tracking-wider" title="Clear Chat History">🗑️ Clear</button>
            </div>
        </div>
        <div id="conv-list" class="flex-1 overflow-y-auto p-2.5 space-y-1.5 custom-scrollbar">
            <div class="text-xs text-gray-400 dark:text-white/30 p-4 text-center">Loading conversations...</div>
        </div>
    </div>

    {{-- Main Chat Area --}}
    <div class="flex-1 flex flex-col bg-white dark:bg-white/[0.02] rounded-2xl border border-gray-200 dark:border-white/[0.06] backdrop-blur-md overflow-hidden shadow-sm">
        {{-- Header --}}
        <div class="bg-slate-50 dark:bg-gradient-to-r dark:from-violet-950/20 dark:to-indigo-950/20 p-4 border-b border-gray-200 dark:border-white/[0.06] flex-shrink-0 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-indigo-600 rounded-xl flex items-center justify-center text-lg shadow-lg shadow-violet-500/15 text-white">🤖</div>
                <div>
                    <h2 class="font-bold text-sm text-gray-800 dark:text-white/95" style="font-family: var(--font-display);">AI Chatbot</h2>
                    <div class="flex items-center gap-1.5 text-[10px] text-gray-500 dark:text-white/40 mt-0.5">
                        <span id="status-dot" class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                        <span id="status-text" class="font-medium">Connecting...</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="startNewChat()" class="md:hidden text-[10px] bg-gray-150 dark:bg-white/10 text-gray-700 dark:text-white/80 px-2.5 py-1.5 rounded-lg hover:bg-gray-200 dark:hover:bg-white/20 transition font-bold uppercase tracking-wider">+ New</button>
                <button onclick="confirmClearHistory()" class="md:hidden text-[10px] bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 px-2.5 py-1.5 rounded-lg hover:bg-rose-600 hover:text-white transition font-bold uppercase tracking-wider">Clear</button>
            </div>
        </div>

        {{-- Messages --}}
        <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50/30 dark:bg-transparent custom-scrollbar">
            <div class="flex items-start gap-3 chat-msg-enter">
                <div class="w-8 h-8 bg-violet-500/10 dark:bg-violet-500/20 text-violet-600 dark:text-violet-300 rounded-lg flex items-center justify-center text-xs flex-shrink-0">🤖</div>
                <div class="bg-white dark:bg-white/[0.03] border border-gray-200 dark:border-white/[0.06] rounded-2xl rounded-tl-sm px-4 py-3 max-w-lg shadow-sm">
                    <div class="text-xs text-gray-700 dark:text-white/85 leading-relaxed">Namaste! 👋 I'm your <strong>AI Chatbot</strong>. I can answer <strong>any question</strong> — academics, coding, general knowledge, and LMS help. Just ask!</div>
                    <p class="text-[9px] text-gray-400 dark:text-white/30 mt-2 font-medium">Just now</p>
                </div>
            </div>
            @foreach($recentChats as $log)
            <div class="flex items-end justify-end gap-3 chat-msg-enter">
                <div class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white rounded-2xl rounded-br-sm px-4 py-2.5 max-w-lg shadow-md">
                    <p class="text-xs leading-relaxed">{{ $log->message }}</p>
                    <p class="text-[9px] text-white/50 mt-1 text-right">{{ $log->created_at->format('h:i A') }}</p>
                </div>
            </div>
            <div class="flex items-start gap-3 chat-msg-enter">
                <div class="w-8 h-8 bg-violet-500/10 dark:bg-violet-500/20 text-violet-600 dark:text-violet-300 rounded-lg flex items-center justify-center text-xs flex-shrink-0">🤖</div>
                <div class="bg-white dark:bg-white/[0.03] border border-gray-200 dark:border-white/[0.06] rounded-2xl rounded-tl-sm px-4 py-3 max-w-lg shadow-sm">
                    <div class="text-xs text-gray-700 dark:text-white/85 leading-relaxed ai-response prose prose-invert prose-sm max-w-none">{!! $log->response !!}</div>
                    @if($log->source)<span class="inline-block mt-1.5 text-[9px] bg-violet-500/10 dark:bg-violet-500/15 text-violet-600 dark:text-violet-300 px-2 py-0.5 rounded-md font-bold">{{ $log->source }}</span>@endif
                    <p class="text-[9px] text-gray-400 dark:text-white/30 mt-1.5">{{ $log->created_at->format('h:i A') }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Typing Indicator --}}
        <div id="typing-indicator" class="hidden px-4 py-3 border-t border-gray-150 dark:border-white/[0.04] bg-white/50 dark:bg-transparent">
            <div class="flex items-center gap-2.5 text-[10px] text-gray-500 dark:text-white/40">
                <div class="flex gap-1"><span class="w-1.5 h-1.5 bg-violet-500 dark:bg-violet-400 rounded-full animate-bounce" style="animation-delay:0ms"></span><span class="w-1.5 h-1.5 bg-violet-500 dark:bg-violet-400 rounded-full animate-bounce" style="animation-delay:150ms"></span><span class="w-1.5 h-1.5 bg-violet-500 dark:bg-violet-400 rounded-full animate-bounce" style="animation-delay:300ms"></span></div>
                <span class="font-semibold">AI is crafting an answer...</span>
            </div>
        </div>

        {{-- Quick Suggestions --}}
        <div id="suggestions" class="px-4 py-2.5 border-t border-gray-150 dark:border-white/[0.04] bg-slate-50/50 dark:bg-white/[0.01] flex gap-2 overflow-x-auto flex-shrink-0 custom-scrollbar">
            @foreach(['Who is Albert Einstein?', 'Explain polymorphism', 'My enrolled courses', 'What is photosynthesis?', 'Write Python hello world'] as $s)
            <button onclick="sendQuick('{{ $s }}')" class="text-[10px] bg-white dark:bg-white/[0.04] border border-gray-200 dark:border-white/[0.06] text-gray-600 dark:text-white/60 px-3 py-1.5 rounded-full hover:bg-violet-500/10 hover:text-violet-700 dark:hover:bg-violet-500/20 dark:hover:text-white hover:border-violet-500/20 transition-all font-semibold whitespace-nowrap flex-shrink-0 shadow-sm">{{ $s }}</button>
            @endforeach
        </div>

        {{-- Input --}}
        <div class="p-3.5 border-t border-gray-150 dark:border-white/[0.06] bg-slate-50/50 dark:bg-white/[0.01] flex-shrink-0">
            <form id="chat-form" class="flex gap-2">
                @csrf
                <input type="text" id="chat-input" placeholder="Ask me anything..." class="flex-1 px-4 py-3 bg-white dark:bg-white/[0.03] border border-gray-200 dark:border-white/[0.08] rounded-xl text-gray-800 dark:text-white/90 placeholder-gray-400 dark:placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-xs" maxlength="2000" autocomplete="off">
                <button type="submit" id="send-btn" class="px-4 py-3 bg-gradient-to-r from-violet-600 to-indigo-600 text-white rounded-xl hover:from-violet-500 hover:to-indigo-500 hover:shadow-lg hover:shadow-indigo-500/10 transition font-bold text-xs disabled:opacity-40 uppercase tracking-wider" disabled>Send</button>
            </form>
        </div>
    </div>
</div>

{{-- Clear History Confirmation Modal --}}
<div id="clear-confirm-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm animate-fade-in">
    <div class="bg-white dark:bg-[#11142a] border border-gray-200 dark:border-white/[0.08] rounded-2xl p-6 max-w-sm w-full mx-4 shadow-2xl text-center">
        <div class="w-12 h-12 bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 rounded-full flex items-center justify-center mx-auto text-xl mb-4">⚠️</div>
        <h3 class="text-sm font-bold text-gray-800 dark:text-white leading-tight">Clear Chat History?</h3>
        <p class="text-[11px] text-gray-500 dark:text-white/60 mt-2 leading-relaxed">This will delete all conversations and logs from the server. This action is permanent and cannot be undone.</p>
        <div class="flex gap-2.5 mt-5 justify-center">
            <button onclick="closeClearModal()" class="px-4 py-2 bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-white/80 text-[10px] font-bold rounded-lg hover:bg-gray-200 dark:hover:bg-white/20 transition uppercase tracking-wider">Cancel</button>
            <button onclick="executeClearHistory()" class="px-4 py-2 bg-rose-650 text-white text-[10px] font-bold rounded-lg hover:bg-rose-500 transition uppercase tracking-wider">Yes, Delete All</button>
        </div>
    </div>
</div>

{{-- Marked.js + Highlight.js CDN --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
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
    let displaySource = source;
    if (source === 'Gemini AI' || source === 'Groq AI') {
        displaySource = 'LMS AI';
    } else if (source === 'Wikipedia' || source === 'DuckDuckGo') {
        displaySource = 'Web Reference';
    } else if (source === 'Knowledge Base') {
        displaySource = 'LMS Knowledge';
    }

    const colors = { 
        'LMS AI': 'bg-indigo-500/10 text-indigo-600 border border-indigo-500/15 dark:bg-indigo-500/15 dark:text-indigo-300',
        'Web Reference': 'bg-amber-500/10 text-amber-600 border border-amber-500/15 dark:bg-amber-500/15 dark:text-amber-300', 
        'LMS Knowledge': 'bg-purple-500/10 text-purple-600 border border-purple-500/15 dark:bg-purple-500/15 dark:text-purple-300', 
        'LMS System': 'bg-indigo-500/10 text-indigo-600 border border-indigo-500/15 dark:bg-indigo-500/15 dark:text-indigo-300', 
        'Math Engine': 'bg-pink-500/10 text-pink-600 border border-pink-500/15 dark:bg-pink-500/15 dark:text-pink-300' 
    };
    const cls = colors[displaySource] || 'bg-gray-100 text-gray-600 border border-gray-200 dark:bg-white/[0.06] dark:text-white/50 dark:border-white/[0.08]';
    const icons = { 'LMS AI': '✨', 'Web Reference': '🔍', 'LMS Knowledge': '📚', 'LMS System': '🎓', 'Math Engine': '🔢' };
    const icon = icons[displaySource] || '💡';
    return `<span class="inline-block mt-1.5 text-[9px] font-bold ${cls} px-2 py-0.5 rounded-md">${icon} ${displaySource}</span>`;
}

// Append message
function appendMsg(content, isUser, source) {
    const time = new Date().toLocaleTimeString('en', {hour:'2-digit', minute:'2-digit'});
    const div = document.createElement('div');
    if (isUser) {
        div.className = 'flex items-end justify-end gap-3 chat-msg-enter';
        div.innerHTML = `<div class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white rounded-2xl rounded-br-sm px-4 py-2.5 max-w-lg shadow-md"><p class="text-xs leading-relaxed">${content.replace(/</g,'&lt;')}</p><p class="text-[9px] text-white/50 mt-1 text-right">${time}</p></div>`;
    } else {
        div.className = 'flex items-start gap-3 chat-msg-enter';
        const badge = sourceBadge(source);
        div.innerHTML = `<div class="w-8 h-8 bg-violet-500/10 dark:bg-violet-500/20 text-violet-600 dark:text-violet-300 rounded-lg flex items-center justify-center text-xs flex-shrink-0">🤖</div><div class="bg-white dark:bg-[#151936] border border-gray-200 dark:border-white/[0.06] rounded-2xl rounded-tl-sm px-4 py-3 max-w-lg shadow-md"><div class="text-xs text-gray-700 dark:text-white/85 leading-relaxed ai-response prose prose-invert prose-sm max-w-none">${renderMd(content)}</div>${badge}<div class="flex items-center justify-between mt-2 pt-1 border-t border-gray-150 dark:border-white/[0.04]"><p class="text-[9px] text-gray-400 dark:text-white/30">${time}</p><button onclick="copyResponse(this)" class="text-[9px] text-violet-600 dark:text-violet-400 font-semibold hover:underline">📋 Copy</button></div></div>`;
    }
    chatMessages.appendChild(div);
    scrollBottom();
}

function copyResponse(btn) {
    const text = btn.closest('div').parentElement.querySelector('.ai-response').innerText;
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
            loadConversations();
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
            chatMessages.innerHTML = `<div class="flex items-start gap-3 chat-msg-enter"><div class="w-8 h-8 bg-violet-500/10 dark:bg-violet-500/20 text-violet-600 dark:text-violet-300 rounded-lg flex items-center justify-center text-xs flex-shrink-0">🤖</div><div class="bg-white dark:bg-[#151936] border border-gray-200 dark:border-white/[0.06] rounded-2xl rounded-tl-sm px-4 py-3 max-w-lg shadow-md"><div class="text-xs text-gray-700 dark:text-white/85">New conversation started! 🎉 Ask me anything.</div></div></div>`;
            loadConversations();
        }
    } catch(e) { console.error(e); }
}

// Clear History Confirmation Modal helpers
function confirmClearHistory() {
    document.getElementById('clear-confirm-modal').classList.remove('hidden');
}

function closeClearModal() {
    document.getElementById('clear-confirm-modal').classList.add('hidden');
}

async function executeClearHistory() {
    closeClearModal();
    try {
        const res = await fetch('{{ route("student.chatbot.clear") }}', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json' 
            },
            body: JSON.stringify({ all: true })
        });
        const data = await res.json();
        if (data.success) {
            conversationId = data.conversation_id;
            chatMessages.innerHTML = `<div class="flex items-start gap-3 chat-msg-enter"><div class="w-8 h-8 bg-violet-500/10 dark:bg-violet-500/20 text-violet-600 dark:text-violet-300 rounded-lg flex items-center justify-center text-xs flex-shrink-0">🤖</div><div class="bg-white dark:bg-[#151936] border border-gray-200 dark:border-white/[0.06] rounded-2xl rounded-tl-sm px-4 py-3 max-w-lg shadow-md"><div class="text-xs text-gray-700 dark:text-white/85">Chat history cleared successfully! 🎉 Ask me anything.</div></div></div>`;
            loadConversations();
        }
    } catch(e) { console.error(e); }
}

// Delete single conversation
async function deleteConversation(id) {
    if (!confirm('Delete this conversation?')) return;
    try {
        const res = await fetch('{{ route("student.chatbot.clear") }}', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json' 
            },
            body: JSON.stringify({ conversation_id: id })
        });
        const data = await res.json();
        if (data.success) {
            if (id === conversationId) {
                conversationId = data.conversation_id;
                chatMessages.innerHTML = `<div class="flex items-start gap-3 chat-msg-enter"><div class="w-8 h-8 bg-violet-500/10 dark:bg-violet-500/20 text-violet-600 dark:text-violet-300 rounded-lg flex items-center justify-center text-xs flex-shrink-0">🤖</div><div class="bg-white dark:bg-[#151936] border border-gray-200 dark:border-white/[0.06] rounded-2xl rounded-tl-sm px-4 py-3 max-w-lg shadow-md"><div class="text-xs text-gray-700 dark:text-white/85">Conversation deleted! Start fresh below.</div></div></div>`;
            }
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
            list.innerHTML = data.conversations.map(c => `
                <div class="group relative flex items-center gap-1 w-full">
                    <button onclick="loadConversation('${c.id}')" class="flex-1 text-left px-3 py-2.5 rounded-xl text-xs hover:bg-gray-100 dark:hover:bg-white/[0.04] transition ${c.id===conversationId?'bg-gray-150 dark:bg-white/[0.04] text-violet-600 dark:text-violet-400 border border-gray-200 dark:border-white/[0.06] font-bold shadow-sm':'text-gray-600 dark:text-white/60'}">
                        <p class="truncate pr-4">${c.title}</p>
                        <p class="text-[9px] opacity-60 mt-0.5">${c.message_count} messages</p>
                    </button>
                    <button onclick="event.stopPropagation(); deleteConversation('${c.id}')" class="absolute right-2 opacity-0 group-hover:opacity-100 text-gray-400 hover:text-rose-500 p-1 rounded-md hover:bg-rose-500/10 transition duration-200 text-[10px]" title="Delete Chat">🗑️</button>
                </div>
            `).join('');
        } else { list.innerHTML = '<div class="text-xs text-gray-400 dark:text-white/30 p-3 text-center">No conversations yet</div>'; }
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

// Dynamic online/offline network detection
function checkStatus() {
    if (navigator.onLine) {
        document.getElementById('status-dot').className = 'w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse';
        document.getElementById('status-text').textContent = 'Online AI Mode';
    } else {
        document.getElementById('status-dot').className = 'w-1.5 h-1.5 bg-amber-400 rounded-full';
        document.getElementById('status-text').textContent = 'Offline AI Mode';
    }
}

// Register dynamic status listeners
window.addEventListener('online', checkStatus);
window.addEventListener('offline', checkStatus);

// Init
checkStatus();
loadConversations();
</script>
@endpush
@endsection
