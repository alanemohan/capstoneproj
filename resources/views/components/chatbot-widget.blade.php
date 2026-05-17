{{-- Floating Chatbot Widget — AI-Powered --}}
@php
    $chatRoute = match(auth()->user()?->role) {
        'admin'   => 'admin.chatbot.chat',
        'teacher' => 'teacher.chatbot.chat',
        default   => 'student.chatbot.chat',
    };
    $chatUrl = '';
    try { $chatUrl = route($chatRoute); } catch (\Throwable $e) {
        try { $chatUrl = route('student.chatbot.chat'); } catch (\Throwable $e2) { $chatUrl = ''; }
    }
@endphp

@if($chatUrl)
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css">
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

<div id="lms-chatbot-widget" x-data="lmsChatbot('{{ $chatUrl }}', '{{ csrf_token() }}')" class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-3" style="font-family:'Inter',sans-serif;">
    {{-- Main Chat Window --}}
    <div x-show="open" x-transition x-cloak class="w-80 sm:w-96 bg-white dark:bg-[#11142a] rounded-2xl shadow-2xl border border-gray-200 dark:border-white/[0.08] overflow-hidden flex flex-col" style="height:520px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-violet-600 to-indigo-600 dark:from-violet-900/80 dark:to-indigo-900/80 px-4 py-3 flex items-center gap-3 flex-shrink-0 text-white">
            <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center text-lg flex-shrink-0">🤖</div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-sm leading-tight">AI Assistant</p>
                <p class="text-[10px] text-white/70 flex items-center gap-1.5 mt-0.5">
                    <span class="w-1.5 h-1.5 rounded-full inline-block animate-pulse" :class="isOnline ? 'bg-emerald-400' : 'bg-amber-400'"></span>
                    <span x-text="isOnline ? 'Online AI Mode' : 'Offline AI Mode'" class="font-medium"></span>
                </p>
            </div>
            <button @click="newChat()" class="text-[10px] bg-white/20 px-2 py-1 rounded-md hover:bg-white/35 transition font-bold uppercase tracking-wider">New</button>
            <button @click="open=false" class="text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50 dark:bg-transparent custom-scrollbar" x-ref="messages">
            <div class="flex gap-2.5 items-start">
                <div class="w-7 h-7 rounded-lg bg-violet-500/10 dark:bg-violet-500/20 text-violet-600 dark:text-violet-300 flex items-center justify-center text-xs flex-shrink-0">🤖</div>
                <div class="bg-white dark:bg-white/[0.03] border border-gray-200 dark:border-white/[0.06] rounded-2xl rounded-tl-sm px-3.5 py-2 text-xs text-gray-700 dark:text-white/85 shadow-sm max-w-[80%]">
                    Namaste! 👋 I'm your AI Assistant. Ask me <strong>anything</strong>!
                </div>
            </div>
            <template x-for="msg in messages" :key="msg.id">
                <div :class="msg.role==='user' ? 'flex justify-end' : 'flex gap-2.5 items-start'">
                    <div x-show="msg.role==='bot'" class="w-7 h-7 rounded-lg bg-violet-500/10 dark:bg-violet-500/20 text-violet-600 dark:text-violet-300 flex items-center justify-center text-xs flex-shrink-0">🤖</div>
                    <div :class="msg.role==='user' ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white rounded-2xl rounded-tr-sm px-3.5 py-2 text-xs max-w-[80%] shadow-sm' : 'bg-white dark:bg-[#151936] border border-gray-200 dark:border-white/[0.06] rounded-2xl rounded-tl-sm px-3.5 py-2 text-xs text-gray-700 dark:text-white/85 shadow-sm max-w-[80%] leading-relaxed'" x-html="msg.role==='user' ? msg.text.replace(/</g,'&lt;') : renderMd(msg.text)"></div>
                </div>
            </template>
            <div x-show="typing" class="flex gap-2.5 items-start">
                <div class="w-7 h-7 rounded-lg bg-violet-500/10 dark:bg-violet-500/20 text-violet-600 dark:text-violet-300 flex items-center justify-center text-xs flex-shrink-0">🤖</div>
                <div class="bg-white dark:bg-[#151936] border border-gray-200 dark:border-white/[0.06] rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm">
                    <div class="flex gap-1.5"><span class="w-1.5 h-1.5 bg-violet-400 rounded-full animate-bounce" style="animation-delay:0ms"></span><span class="w-1.5 h-1.5 bg-violet-400 rounded-full animate-bounce" style="animation-delay:150ms"></span><span class="w-1.5 h-1.5 bg-violet-400 rounded-full animate-bounce" style="animation-delay:300ms"></span></div>
                </div>
            </div>
        </div>

        {{-- Input --}}
        <div class="border-t border-gray-200 dark:border-white/[0.06] p-3 bg-white dark:bg-white/[0.01] flex-shrink-0">
            <div class="flex gap-2 items-center">
                <input type="text" x-model="input" @keydown.enter="sendMessage()" :disabled="loading" placeholder="Ask me anything…" class="flex-1 bg-gray-50 dark:bg-white/[0.03] border border-gray-200 dark:border-white/[0.08] rounded-xl px-4 py-2.5 text-xs text-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-violet-400/40 disabled:opacity-50 transition">
                <button @click="sendMessage()" :disabled="loading||!input.trim()" class="w-10 h-10 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white rounded-xl flex items-center justify-center transition disabled:opacity-40 flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Trigger Floating Button --}}
    <button @click="open=!open; if(open) $nextTick(()=>scrollBottom())" class="w-14 h-14 rounded-full shadow-2xl flex items-center justify-center transition-all duration-300 relative text-white" :class="open?'bg-gray-800 hover:bg-gray-700':'bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500'" title="AI Assistant" style="box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);">
        <span x-show="!open" class="text-2xl">🤖</span>
        <svg x-show="open" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        <span x-show="!open" class="absolute inset-0 rounded-full bg-violet-400 animate-ping opacity-25"></span>
    </button>
</div>

<script>
function lmsChatbot(chatUrl, csrfToken) {
    return {
        open: false, input: '', loading: false, typing: false, messages: [], msgCounter: 0, isOnline: true, convId: null,

        init() {
            this.convId = 'widget-' + Date.now();
            marked.setOptions({breaks:true, gfm:true, highlight:(code,lang)=>{try{return lang?hljs.highlight(code,{language:lang}).value:hljs.highlightAuto(code).value}catch(e){return code}}});
            this.checkOnline();
            
            // Native dynamic network state check
            window.addEventListener('online', () => { this.isOnline = true; });
            window.addEventListener('offline', () => { this.isOnline = false; });
        },

        renderMd(text) { try{return marked.parse(text||'')}catch(e){return (text||'').replace(/\n/g,'<br>')} },

        checkOnline() {
            this.isOnline = navigator.onLine;
        },

        async newChat() {
            this.convId = 'widget-' + Date.now();
            this.messages = [];
        },

        async sendMessage() {
            const text=this.input.trim(); if(!text||this.loading)return;
            this.messages.push({id:++this.msgCounter, role:'user', text});
            this.input=''; this.loading=true; this.typing=true;
            this.$nextTick(()=>this.scrollBottom());
            try {
                const resp=await fetch(chatUrl,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},body:JSON.stringify({message:text,conversation_id:this.convId})});
                const data=await resp.json(); this.typing=false;
                if(data.success||data.response){this.messages.push({id:++this.msgCounter, role:'bot', text:data.response, source:data.source})}
                else{this.messages.push({id:++this.msgCounter, role:'bot', text:'⚠️ Sorry, please try again.'})}
            }catch(e){this.typing=false;this.messages.push({id:++this.msgCounter, role:'bot', text:'⚠️ Connection error. Please try again.'})}
            this.loading=false;this.$nextTick(()=>this.scrollBottom());
        },

        scrollBottom(){const el=this.$refs.messages; if(el) el.scrollTop=el.scrollHeight},
    };
}
</script>
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(156, 163, 175, 0.2);
        border-radius: 9999px;
    }
    [x-cloak] { display: none !important; }
</style>
@endif
