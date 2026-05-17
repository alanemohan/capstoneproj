@extends('layouts.admin')

@section('title', __('messages.translations') . ' - Nabha Admin')

@section('admin-content')
<div class="space-y-6 animate-fade-in text-slate-800" x-data="{ activeTab: 'announcements' }">
    <div class="flex items-center justify-between pb-5 border-b border-slate-200 flex-wrap gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight" style="font-family: var(--font-display);">{{ __('messages.translations') }}</h1>
            <p class="text-xs text-slate-500 mt-1 font-semibold">{{ __('messages.manage') }} dynamic content translations.</p>
        </div>
        <div class="flex gap-2">
            <button @click="$dispatch('open-modal', 'bulk-translate')" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-lg transition text-xs font-bold uppercase tracking-wider shadow-sm">
                Bulk Translate All
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @foreach($stats as $key => $stat)
        <div @click="activeTab = '{{ $key }}'" 
             :class="activeTab === '{{ $key }}' ? 'border-orange-500 bg-orange-50/10' : 'bg-white hover:bg-slate-50'"
             class="p-5 rounded-xl border border-slate-200 cursor-pointer transition shadow-sm">
            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wider capitalize">{{ str_replace('_', ' ', $key) }}</div>
            <div class="mt-2 flex items-baseline justify-between">
                <div class="text-xl font-extrabold text-slate-900 tabular-nums">{{ $stat['total'] }}</div>
                @if($stat['pending'] > 0)
                <div class="text-[9px] font-bold text-amber-705 bg-amber-50 border border-amber-250 px-2 py-0.5 rounded-md uppercase tracking-wider">
                    {{ $stat['pending'] }} pending
                </div>
                @else
                <div class="text-[9px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-250 px-2 py-0.5 rounded-md uppercase tracking-wider">
                    Complete
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Content Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="p-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider capitalize" x-text="activeTab"></h3>
            <form action="{{ route('admin.translations.bulk') }}" method="POST" class="inline">
                @csrf
                <template x-if="activeTab === 'announcements'">
                    <div>
                        <input type="hidden" name="model" value="App\Models\Announcement">
                        <input type="hidden" name="fields[]" value="title">
                        <input type="hidden" name="fields[]" value="content">
                    </div>
                </template>
                <template x-if="activeTab === 'courses'">
                    <div>
                        <input type="hidden" name="model" value="App\Models\Course">
                        <input type="hidden" name="fields[]" value="title">
                        <input type="hidden" name="fields[]" value="description">
                    </div>
                </template>
                <template x-if="activeTab === 'lessons'">
                    <div>
                        <input type="hidden" name="model" value="App\Models\Lesson">
                        <input type="hidden" name="fields[]" value="title">
                        <input type="hidden" name="fields[]" value="description">
                    </div>
                </template>
                <template x-if="activeTab === 'quizzes'">
                    <div>
                        <input type="hidden" name="model" value="App\Models\Quiz">
                        <input type="hidden" name="fields[]" value="title">
                        <input type="hidden" name="fields[]" value="description">
                    </div>
                </template>
                <button type="submit" class="text-[10px] font-bold uppercase tracking-wider text-orange-600 hover:text-orange-755 transition">
                    Re-translate all in this category
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            @foreach($stats as $key => $stat)
            <div x-show="activeTab === '{{ $key }}'" x-cloak>
                <table class="w-full text-xs text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-400 uppercase font-bold tracking-wider">
                            <th class="px-5 py-3">ID</th>
                            <th class="px-5 py-3">Source (EN)</th>
                            <th class="px-5 py-3">Hindi</th>
                            <th class="px-5 py-3">Punjabi</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-150">
                        @php
                            $items = ($stat['model'])::latest()->paginate(10, ['*'], $key . '_page');
                        @endphp
                        @forelse($items as $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-4 text-slate-400 font-bold tabular-nums">#{{ $item->id }}</td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-slate-900 leading-snug" style="font-family: var(--font-display);">{{ $item->title }}</div>
                                <div class="text-[10px] text-slate-450 font-semibold line-clamp-1 mt-0.5 leading-relaxed">{{ strip_tags($item->description ?? $item->content) }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-xs text-slate-700 font-devanagari font-semibold leading-relaxed">{{ $item->title_hi ?: '---' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-xs text-slate-700 font-gurmukhi font-semibold leading-relaxed">{{ $item->title_pa ?: '---' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                @php $status = $item->getTranslationStatus($stat['fields']); @endphp
                                @if($status === 'complete')
                                    <span class="px-2.5 py-0.5 border border-emerald-250 bg-emerald-50 text-emerald-700 text-[9px] font-bold rounded-md uppercase tracking-wider">Complete</span>
                                @elseif($status === 'partial')
                                    <span class="px-2.5 py-0.5 border border-amber-250 bg-amber-50 text-amber-750 text-[9px] font-bold rounded-md uppercase tracking-wider">Partial</span>
                                @else
                                    <span class="px-2.5 py-0.5 border border-rose-250 bg-rose-50 text-rose-700 text-[9px] font-bold rounded-md uppercase tracking-wider">Missing</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end">
                                    <form action="{{ route('admin.translations.retranslate') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="model" value="{{ $stat['model'] }}">
                                        <input type="hidden" name="id" value="{{ $item->id }}">
                                        @foreach($stat['fields'] as $f)
                                            <input type="hidden" name="fields[]" value="{{ $f }}">
                                        @endforeach
                                        <button type="submit" title="Auto-translate" class="text-[10px] font-bold uppercase tracking-wider text-orange-600 hover:text-orange-755 px-2.5 py-1.5 bg-orange-50 border border-orange-150 rounded-md transition shadow-sm">
                                            Retranslate
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-semibold italic">No records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-5 py-4 border-t border-slate-150 bg-slate-50/50">
                    {{ $items->links() }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .font-devanagari { font-family: 'Noto Sans Devanagari', sans-serif; }
    .font-gurmukhi { font-family: 'Noto Sans Gurmukhi', sans-serif; }
</style>
@endpush
@endsection
