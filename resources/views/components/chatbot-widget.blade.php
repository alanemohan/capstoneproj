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
    <div x-show="open" x-transition x-cloak class="w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden flex flex-col" style="height:520px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.35);">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-4 py-3 flex items-center gap-3 flex-shrink-0">
            <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-xl flex-shrink-0">🤖</div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-white text-sm">AI Assistant</p>
                <p class="text-indigo-200 text-xs flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full inline-block animate-pulse"></span>
                    <span x-text="isOnline ? 'Online • AI Ready' : 'Offline • Local Mode'"></span>
                </p>
            </div>
            <button @click="newChat()" class="text-xs bg-white/20 px-2 py-1 rounded-md text-white hover:bg-white/30 transition">New</button>
            <button @click="open=false" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50" x-ref="messages">
            <div class="flex gap-2 items-start">
                <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-sm flex-shrink-0">🤖</div>
                <div class="bg-white border border-gray-200 rounded-2xl rounded-tl-sm px-3 py-2 text-sm text-gray-700 shadow-sm max-w-[80%]">
                    Namaste! 👋 I'm your AI Assistant. Ask me <strong>anything</strong>!
                </div>
            </div>
            <template x-for="msg in messages" :key="msg.id">
                <div :class="msg.role==='user' ? 'flex justify-end' : 'flex gap-2 items-start'">
                    <div x-show="msg.role==='bot'" class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-sm flex-shrink-0">🤖</div>
                    <div :class="msg.role==='user' ? 'bg-indigo-600 text-white rounded-2xl rounded-tr-sm px-3 py-2 text-sm max-w-[80%] shadow-sm' : 'bg-white border border-gray-200 rounded-2xl rounded-tl-sm px-3 py-2 text-sm text-gray-700 shadow-sm max-w-[80%] leading-relaxed'" x-html="msg.role==='user' ? msg.text.replace(/</g,'&lt;') : renderMd(msg.text)"></div>
                </div>
            </template>
            <div x-show="typing" class="flex gap-2 items-start">
                <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-sm flex-shrink-0">🤖</div>
                <div class="bg-white border border-gray-200 rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm">
                    <div class="flex gap-1.5"><span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay:0ms"></span><span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay:150ms"></span><span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay:300ms"></span></div>
                </div>
            </div>
        </div>

        {{-- Input --}}
        <div class="border-t border-gray-200 p-3 bg-white flex-shrink-0">
            <div class="flex gap-2 items-center">
                <input type="text" x-model="input" @keydown.enter="sendMessage()" :disabled="loading" placeholder="Ask me anything…" class="flex-1 bg-gray-100 border-0 rounded-xl px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 disabled:opacity-50">
                <button @click="sendMessage()" :disabled="loading||!input.trim()" class="w-10 h-10 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 text-white rounded-xl flex items-center justify-center transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </div>
        </div>
    </div>

    <button @click="open=!open;if(open)$nextTick(()=>scrollBottom())" class="w-14 h-14 rounded-full shadow-2xl flex items-center justify-center transition-all duration-300 relative" :class="open?'bg-gray-800 hover:bg-gray-700':'bg-indigo-600 hover:bg-indigo-700'" style="box-shadow:0 8px 25px rgba(99,102,241,0.5);">
        <span x-show="!open" class="text-2xl">🤖</span>
        <svg x-show="open" x-cloak class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        <span x-show="!open" class="absolute inset-0 rounded-full bg-indigo-400 animate-ping opacity-20"></span>
    </button>
</div>

<script>
function lmsChatbot(chatUrl, csrfToken) {
    return {
        open:false, input:'', loading:false, typing:false, messages:[], msgCounter:0, isOnline:true, convId:null,

        init() {
            this.convId = 'widget-' + Date.now();
            marked.setOptions({breaks:true,gfm:true,highlight:(code,lang)=>{try{return lang?hljs.highlight(code,{language:lang}).value:hljs.highlightAuto(code).value}catch(e){return code}}});
            this.checkOnline();
            setInterval(()=>this.checkOnline(), 30000);
        },

        renderMd(text) { try{return marked.parse(text||'')}catch(e){return (text||'').replace(/\n/g,'<br>')} },

        async checkOnline() { try{await fetch('https://api.groq.com',{mode:'no-cors',cache:'no-cache'});this.isOnline=true}catch(e){this.isOnline=false} },

        async newChat() {
            this.convId = 'widget-' + Date.now();
            this.messages = [];
        },

        async sendMessage() {
            const text=this.input.trim(); if(!text||this.loading)return;
            this.messages.push({id:++this.msgCounter,role:'user',text});
            this.input=''; this.loading=true; this.typing=true;
            this.$nextTick(()=>this.scrollBottom());
            try {
                const resp=await fetch(chatUrl,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},body:JSON.stringify({message:text,conversation_id:this.convId})});
                const data=await resp.json(); this.typing=false;
                if(data.success||data.response){this.messages.push({id:++this.msgCounter,role:'bot',text:data.response,source:data.source})}
                else{this.messages.push({id:++this.msgCounter,role:'bot',text:'⚠️ Sorry, please try again.'})}
            }catch(e){this.typing=false;this.messages.push({id:++this.msgCounter,role:'bot',text:'⚠️ Connection error. Please try again.'})}
            this.loading=false;this.$nextTick(()=>this.scrollBottom());
        },

        scrollBottom(){const el=this.$refs.messages;if(el)el.scrollTop=el.scrollHeight},
    };
}
</script>
<style>.scrollbar-hide::-webkit-scrollbar{display:none}.scrollbar-hide{-ms-overflow-style:none;scrollbar-width:none}[x-cloak]{display:none!important}</style>
@endif
