@extends('layouts.admin')

@section('title', __('messages.scholarships_cms'))

@php($confirmActionText = __('messages.confirm_action'))

@section('admin-content')
<div class="space-y-6 animate-fade-in text-slate-800" x-data="{ 
    showModal: false, 
    editMode: false,
    activeTab: 'en',
    form: {
        id: '', title: '', title_hi: '', title_pa: '', 
        amount: '', amount_hi: '', amount_pa: '',
        deadline: '', 
        description: '', description_hi: '', description_pa: '',
        eligibility_criteria: '', eligibility_criteria_hi: '', eligibility_criteria_pa: '',
        url: ''
    },
    openCreate() {
        this.editMode = false;
        this.activeTab = 'en';
        this.form = { 
            id: '', title: '', title_hi: '', title_pa: '', 
            amount: '', amount_hi: '', amount_pa: '',
            deadline: '', 
            description: '', description_hi: '', description_pa: '',
            eligibility_criteria: '', eligibility_criteria_hi: '', eligibility_criteria_pa: '',
            url: '' 
        };
        this.showModal = true;
    },
    openEdit(item) {
        this.editMode = true;
        this.activeTab = 'en';
        let formattedItem = { ...item };
        if (formattedItem.deadline) {
            formattedItem.deadline = formattedItem.deadline.split('T')[0];
        }
        this.form = formattedItem;
        this.showModal = true;
    }
}">
    <div class="flex items-center justify-between pb-5 border-b border-slate-200 flex-wrap gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight" style="font-family: var(--font-display);">{{ __('messages.scholarships_cms') }}</h1>
            <p class="text-xs text-slate-500 mt-1 font-semibold">{{ __('messages.manage_scholarships_subtitle') }}</p>
        </div>
        <button @click="openCreate()" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-lg transition text-xs font-bold uppercase tracking-wider shadow-sm">
            + {{ __('messages.add_scholarship') }}
        </button>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-755 px-4.5 py-3 rounded-lg text-xs font-semibold animate-fade-in">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-400 uppercase font-bold tracking-wider">
                        <th class="px-5 py-3">{{ __('messages.title_en') }}</th>
                        <th class="px-5 py-3">{{ __('messages.amount') }}</th>
                        <th class="px-5 py-3">{{ __('messages.translations') }}</th>
                        <th class="px-5 py-3">{{ __('messages.deadline') }}</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-150">
                    @forelse($scholarships as $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-4 font-bold text-slate-900" style="font-family: var(--font-display);">{{ $item->title }}</td>
                            <td class="px-5 py-4 text-slate-700 font-semibold">{{ $item->amount }}</td>
                            <td class="px-5 py-4">
                                <div class="flex gap-1.5">
                                    <span class="px-2 py-0.5 border rounded-md text-[9px] font-bold tracking-wider {{ $item->title_hi ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-slate-50 border-slate-200 text-slate-400' }}">HI</span>
                                    <span class="px-2 py-0.5 border rounded-md text-[9px] font-bold tracking-wider {{ $item->title_pa ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-slate-50 border-slate-200 text-slate-400' }}">PA</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-500 tabular-nums">{{ \Carbon\Carbon::parse($item->deadline)->format('d M, Y') }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEdit({{ json_encode($item) }})" class="text-[10px] font-bold uppercase tracking-wider text-orange-600 hover:text-orange-755 px-2.5 py-1.5 bg-orange-50 border border-orange-150 rounded-md transition shadow-sm">{{ __('messages.edit') }}</button>
                                    <form method="POST" action="{{ route('admin.scholarships.destroy', $item->id) }}" onsubmit="return confirm('{{ $confirmActionText }}')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-[10px] font-bold uppercase tracking-wider text-red-500 hover:text-red-750 px-2.5 py-1.5 bg-red-50 border border-red-150 rounded-md transition shadow-sm">{{ __('messages.delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-semibold">{{ __('messages.no_scholarships_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($scholarships->hasPages())
            <div class="px-5 py-4 border-t border-slate-150 bg-slate-50/50">{{ $scholarships->links() }}</div>
        @endif
    </div>

    <!-- Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40" x-cloak x-transition>
        <div class="bg-white rounded-xl border border-slate-200 w-full max-w-2xl p-6 mx-4 relative max-h-[90vh] overflow-y-auto shadow-2xl" @click.away="showModal = false">
            <button @click="showModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-655 text-sm transition">✕</button>
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-150 pb-3 mb-4" x-text="editMode ? '{{ __('messages.edit_scholarship') }}' : '{{ __('messages.add_scholarship') }}'"></h2>
            
            <!-- Language Tabs -->
            <div class="flex border-b border-slate-200 mb-4 gap-2">
                <button type="button" @click="activeTab = 'en'" :class="activeTab === 'en' ? 'border-orange-500 text-orange-600 font-bold' : 'border-transparent text-slate-400 hover:text-slate-600'" class="px-4 py-2 border-b-2 font-bold text-xs uppercase tracking-wider transition">{{ __('messages.lang_english') }}</button>
                <button type="button" @click="activeTab = 'hi'" :class="activeTab === 'hi' ? 'border-orange-500 text-orange-600 font-bold' : 'border-transparent text-slate-400 hover:text-slate-600'" class="px-4 py-2 border-b-2 font-bold text-xs uppercase tracking-wider transition">{{ __('messages.lang_hindi') }}</button>
                <button type="button" @click="activeTab = 'pa'" :class="activeTab === 'pa' ? 'border-orange-500 text-orange-600 font-bold' : 'border-transparent text-slate-400 hover:text-slate-600'" class="px-4 py-2 border-b-2 font-bold text-xs uppercase tracking-wider transition">{{ __('messages.lang_punjabi') }}</button>
            </div>

            <form method="POST" :action="editMode ? `/admin/scholarships/${form.id}` : '{{ route('admin.scholarships.store') }}'" class="space-y-4">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                
                <!-- English Fields -->
                <div x-show="activeTab === 'en'" class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('messages.title_en') }}</label>
                        <input type="text" name="title" x-model="form.title" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-xs font-medium text-slate-808">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('messages.amount_en') }}</label>
                        <input type="text" name="amount" x-model="form.amount" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-xs font-medium text-slate-808">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('messages.description_en') }}</label>
                        <textarea name="description" x-model="form.description" required rows="3" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-xs font-medium text-slate-808 resize-none leading-relaxed" placeholder="Scholarship details..."></textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('messages.eligibility_en') }}</label>
                        <textarea name="eligibility_criteria" x-model="form.eligibility_criteria" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-xs font-medium text-slate-808 resize-none leading-relaxed" placeholder="Eligibility guidelines..."></textarea>
                    </div>
                </div>

                <!-- Hindi Fields -->
                <div x-show="activeTab === 'hi'" class="space-y-4" x-cloak>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('messages.title_hi') }}</label>
                        <input type="text" name="title_hi" x-model="form.title_hi" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-xs font-medium text-slate-808">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('messages.amount_hi') }}</label>
                        <input type="text" name="amount_hi" x-model="form.amount_hi" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-xs font-medium text-slate-808">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('messages.description_hi') }}</label>
                        <textarea name="description_hi" x-model="form.description_hi" rows="3" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-xs font-medium text-slate-808 resize-none leading-relaxed"></textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('messages.eligibility_hi') }}</label>
                        <textarea name="eligibility_criteria_hi" x-model="form.eligibility_criteria_hi" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-xs font-medium text-slate-808 resize-none leading-relaxed"></textarea>
                    </div>
                </div>

                <!-- Punjabi Fields -->
                <div x-show="activeTab === 'pa'" class="space-y-4" x-cloak>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('messages.title_pa') }}</label>
                        <input type="text" name="title_pa" x-model="form.title_pa" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-xs font-medium text-slate-808">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('messages.amount_pa') }}</label>
                        <input type="text" name="amount_pa" x-model="form.amount_pa" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-xs font-medium text-slate-808">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('messages.description_pa') }}</label>
                        <textarea name="description_pa" x-model="form.description_pa" rows="3" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-xs font-medium text-slate-808 resize-none leading-relaxed"></textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('messages.eligibility_pa') }}</label>
                        <textarea name="eligibility_criteria_pa" x-model="form.eligibility_criteria_pa" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-xs font-medium text-slate-808 resize-none leading-relaxed"></textarea>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('messages.deadline') }}</label>
                        <input type="date" name="deadline" x-model="form.deadline" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-xs font-medium text-slate-808">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('messages.official_url_optional') }}</label>
                        <input type="url" name="url" x-model="form.url" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-xs font-medium text-slate-808">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-slate-150">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider text-slate-500 border border-slate-300 hover:bg-slate-50 transition">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-sm" x-text="editMode ? '{{ __('messages.update') }}' : '{{ __('messages.save') }}'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
