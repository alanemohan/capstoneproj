@php
use Illuminate\Support\Str;
$accentAdd = $routePrefix === 'admin' ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-emerald-600 hover:bg-emerald-700';
$accentRing = $routePrefix === 'admin' ? 'focus:ring-indigo-500/40 focus:border-indigo-500/30' : 'focus:ring-emerald-500/40 focus:border-emerald-500/30';
@endphp

<div class="space-y-6 max-w-6xl animate-fade-in" x-data="qaManager()">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between flex-wrap gap-3 pb-5 border-b border-gray-200">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight" style="font-family: var(--font-display);">🤖 Chatbot Q&A Training</h1>
            <p class="text-xs text-gray-500 mt-1">
                Add questions &amp; answers to train the hybrid intelligent chatbot.
                Total: <span class="font-extrabold text-gray-900 tabular-nums">{{ $qaList->total() }}</span> entries
            </p>
        </div>
        <button @click="showForm = !showForm"
                class="{{ $accentAdd }} text-white px-4 py-2.5 rounded-lg font-bold text-xs uppercase tracking-wider transition shadow-sm flex items-center gap-2">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span x-text="showForm ? 'Cancel' : 'Add New Q&A'"></span>
        </button>
    </div>

    {{-- ── Add / Edit Form ── --}}
    <div x-show="showForm" x-transition x-cloak
         class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <h2 class="text-xs font-bold text-gray-800 uppercase tracking-widest mb-4" x-text="editId ? '✏️ Edit Q&A Entry' : '➕ Add New Q&A Entry'"></h2>

        {{-- Add form --}}
        <form id="qa-add-form" method="POST" action="{{ route($routePrefix . '.chatbot-qa.store') }}"
              x-show="!editId" class="space-y-4">
            @csrf
            @include('chatbot-qa._form-fields', ['accentRing' => $accentRing, 'accentAdd' => $accentAdd, 'btnLabel' => 'Add to Knowledge Base'])
        </form>

        {{-- Edit forms (one per entry, shown via Alpine) --}}
        @foreach($qaList as $qa)
            <form id="qa-edit-form-{{ $qa->id }}" method="POST"
                  action="{{ route($routePrefix . '.chatbot-qa.update', $qa) }}"
                  x-show="editId === {{ $qa->id }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Question / Topic</label>
                    <input type="text" name="question" value="{{ old('question', $qa->question) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 {{ $accentRing }} text-gray-850"
                           placeholder="e.g. what is newton law">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Answer</label>
                    <textarea name="answer" rows="5" required
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 {{ $accentRing }} resize-y leading-relaxed text-gray-850"
                              placeholder="Full answer — supports **bold** and newlines">{{ old('answer', $qa->answer) }}</textarea>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Keywords <span class="font-semibold text-gray-300">(comma-separated trigger words)</span></label>
                    <input type="text" name="keywords" value="{{ old('keywords', $qa->keywords) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 {{ $accentRing }} text-gray-850"
                           placeholder="newton, laws of motion, inertia, force">
                    <p class="text-[10px] text-gray-400 font-semibold mt-1.5">Student can trigger this answer by typing any of these words.</p>
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="{{ $accentAdd }} text-white px-5 py-2.5 rounded-lg font-bold text-xs uppercase tracking-wider transition">
                        Save Changes
                    </button>
                    <button type="button" @click="editId = null; showForm = false"
                            class="px-5 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider text-gray-600 border border-gray-300 hover:bg-gray-50 transition">
                        Cancel
                    </button>
                </div>
            </form>
        @endforeach
    </div>

    {{-- ── Search ── --}}
    <form method="GET" action="{{ route($routePrefix . '.chatbot-qa') }}" data-no-loading class="flex gap-3">
        <input type="text" name="search" value="{{ $search }}"
               placeholder="Search questions or keywords..."
               class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 {{ $accentRing }} text-gray-850">
        <button type="submit" class="px-5 py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-xs font-bold uppercase tracking-wider transition">
            Search
        </button>
        @if($search)
            <a href="{{ route($routePrefix . '.chatbot-qa') }}"
               class="px-4 py-2.5 border border-gray-350 text-gray-600 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-gray-50 transition flex items-center">
                Clear
            </a>
        @endif
    </form>

    {{-- ── Q&A Table ── --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        @if($qaList->isEmpty())
            <div class="py-16 text-center">
                <div class="text-3xl mb-3">🤖</div>
                <h3 class="font-bold text-gray-800 text-sm mb-1">No Q&A entries yet</h3>
                <p class="text-xs text-gray-400">Add your first question and answer to start training the chatbot.</p>
            </div>
        @else
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-[10px] text-gray-400 uppercase font-bold tracking-wider">
                        <th class="px-5 py-3 text-left w-8">#</th>
                        <th class="px-5 py-3 text-left">Question</th>
                        <th class="px-5 py-3 text-left hidden lg:table-cell">Keywords</th>
                        <th class="px-5 py-3 text-left hidden md:table-cell">Added by</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-150">
                    @foreach($qaList as $qa)
                        <tr class="hover:bg-gray-50/50 transition group"
                            :class="editId === {{ $qa->id }} ? 'bg-amber-50/20' : ''">
                            <td class="px-5 py-4 text-gray-400 text-[10px] font-semibold">{{ $qa->id }}</td>
                            <td class="px-5 py-4">
                                <p class="font-bold text-gray-900 leading-snug" style="font-family: var(--font-display);">{{ $qa->question }}</p>
                                <p class="text-[10px] text-gray-500 mt-1.5 leading-relaxed">{{ Str::limit(str_replace(['**', "\n"], ['', ' '], $qa->answer), 90) }}</p>
                            </td>
                            <td class="px-5 py-4 hidden lg:table-cell">
                                @if($qa->keywords)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_slice($qa->keywords_array, 0, 4) as $kw)
                                            <span class="text-[9px] font-bold uppercase tracking-wider bg-gray-50 border border-gray-150 text-gray-600 px-2 py-0.5 rounded-md">{{ $kw }}</span>
                                        @endforeach
                                        @if(count($qa->keywords_array) > 4)
                                            <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider ml-1 self-center">+{{ count($qa->keywords_array) - 4 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-300 italic">none</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 hidden md:table-cell text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                {{ $qa->creator?->name ?? 'Seeder' }}
                                <span class="block text-gray-400 mt-0.5 normal-case font-semibold text-[9px]">{{ $qa->created_at->format('d M Y') }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Edit --}}
                                    <button @click="editId = {{ $qa->id }}; showForm = true; $nextTick(() => $el.closest('.space-y-6').querySelector('#qa-add-form')?.closest('div')?.scrollIntoView({behavior:'smooth'}))"
                                            class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 hover:text-indigo-850 px-2.5 py-1.5 bg-indigo-50 border border-indigo-150 rounded-md transition">
                                        Edit
                                    </button>
                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route($routePrefix . '.chatbot-qa.destroy', $qa) }}"
                                          onsubmit="return confirm('Delete this Q&A entry?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="text-[10px] font-bold uppercase tracking-wider text-red-500 hover:text-red-750 px-2.5 py-1.5 bg-red-50 border border-red-150 rounded-md transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($qaList->hasPages())
                <div class="px-5 py-4 border-t border-gray-150">
                    {{ $qaList->links() }}
                </div>
            @endif
        @endif
    </div>

</div>

@push('scripts')
<script>
function qaManager() {
    return {
        showForm: {{ $errors->any() ? 'true' : 'false' }},
        editId: null,
    };
}
</script>
@endpush
