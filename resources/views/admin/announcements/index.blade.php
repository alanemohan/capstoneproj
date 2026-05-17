@extends('layouts.admin')

@section('title', __('messages.notice_board') . ' CMS')

@section('admin-content')
<div class="space-y-6 animate-fade-in" x-data="{ 
    showModal: false, 
    editMode: false,
    form: { id: '', title: '', content: '', target_class: [] },
    openCreate() {
        this.editMode = false;
        this.form = { id: '', title: '', content: '', target_class: [] };
        this.showModal = true;
    },
    openEdit(item) {
        this.editMode = true;
        this.form = { ...item, target_class: item.target_class ? (Array.isArray(item.target_class) ? item.target_class : JSON.parse(item.target_class)) : [] };
        this.showModal = true;
    }
}">
    <div class="flex items-center justify-between pb-5 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight" style="font-family: var(--font-display);">{{ __('messages.notice_board') }}</h1>
            <p class="text-xs text-slate-500 mt-1 font-semibold">{{ __('messages.manage') }} global system announcements.</p>
        </div>
        <button @click="openCreate()" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-lg transition text-xs font-bold uppercase tracking-wider shadow-sm">
            + {{ __('messages.submit') }} Announcement
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
                        <th class="px-5 py-3">{{ __('messages.title') }}</th>
                        <th class="px-5 py-3">{{ __('messages.benefits_en') }}</th>
                        <th class="px-5 py-3">{{ __('messages.date') }}</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-150">
                    @forelse($announcements as $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-4 font-bold text-slate-900" style="font-family: var(--font-display);">
                                {{ $item->getLocalized('title') }}
                            </td>
                            <td class="px-5 py-4 text-slate-550 leading-relaxed max-w-xs truncate">{{ Str::limit(strip_tags($item->getLocalized('content')), 50) }}</td>
                            <td class="px-5 py-4 text-slate-500 font-semibold tabular-nums">{{ $item->created_at->format('d M, Y H:i') }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEdit({{ json_encode($item) }})" class="text-[10px] font-bold uppercase tracking-wider text-orange-600 hover:text-orange-750 px-2.5 py-1.5 bg-orange-50 border border-orange-150 rounded-md transition">{{ __('messages.edit') }}</button>
                                    <form method="POST" action="{{ route('admin.announcements.destroy', $item->id) }}" onsubmit="return confirm('{{ __('messages.confirm_action') }}')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-[10px] font-bold uppercase tracking-wider text-red-500 hover:text-red-750 px-2.5 py-1.5 bg-red-50 border border-red-150 rounded-md transition">{{ __('messages.delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-semibold">{{ __('messages.no_announcements') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($announcements->hasPages())
            <div class="px-5 py-4 border-t border-slate-150">{{ $announcements->links() }}</div>
        @endif
    </div>

    <!-- Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40" x-cloak x-transition>
        <div class="bg-white rounded-xl border border-slate-200 w-full max-w-lg p-6 mx-4 relative shadow-2xl" @click.away="showModal = false">
            <button @click="showModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition text-sm">✕</button>
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-150 pb-3 mb-4" x-text="editMode ? '{{ __('messages.edit') }} Notice' : '{{ __('messages.submit') }} Notice'"></h2>
            
            <form method="POST" :action="editMode ? `/admin/announcements/${form.id}` : '{{ route('admin.announcements.store') }}'" class="space-y-4">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('messages.title') }} (EN)</label>
                    <input type="text" name="title" x-model="form.title" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-xs font-medium text-slate-808">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('messages.target_audience') }}</label>
                    <select name="target_class[]" x-model="form.target_class" multiple class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-xs font-medium text-slate-808 h-24 bg-white">
                        <option value="All Classes">All Classes</option>
                        @foreach(['Class 6','Class 7','Class 8','Class 9','Class 10'] as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Content (EN)</label>
                    <textarea name="content" x-model="form.content" required rows="4" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-xs font-medium text-slate-850 resize-none leading-relaxed" placeholder="Write the announcement details..."></textarea>
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
