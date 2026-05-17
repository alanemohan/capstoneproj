@extends('layouts.admin')

@section('title', __('messages.translations') . ' - Nabha Admin')

@section('admin-content')
<div class="space-y-6" x-data="{ activeTab: 'announcements' }">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('messages.translations') }}</h1>
            <p class="text-sm text-gray-500">{{ __('messages.manage') }} dynamic content translations</p>
        </div>
        <div class="flex gap-2">
            <button @click="$dispatch('open-modal', 'bulk-translate')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">
                Bulk Translate All
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @foreach($stats as $key => $stat)
        <div @click="activeTab = '{{ $key }}'" 
             :class="activeTab === '{{ $key }}' ? 'ring-2 ring-indigo-500 bg-indigo-50' : 'bg-white hover:bg-gray-50'"
             class="p-4 rounded-xl shadow-sm border border-gray-100 cursor-pointer transition">
            <div class="text-sm font-medium text-gray-500 capitalize">{{ str_replace('_', ' ', $key) }}</div>
            <div class="mt-1 flex items-baseline justify-between">
                <div class="text-2xl font-bold text-gray-800">{{ $stat['total'] }}</div>
                @if($stat['pending'] > 0)
                <div class="text-xs font-semibold text-amber-600 bg-amber-100 px-2 py-0.5 rounded-full">
                    {{ $stat['pending'] }} pending
                </div>
                @else
                <div class="text-xs font-semibold text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full">
                    Complete
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Content Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 capitalize" x-text="activeTab"></h3>
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
                <button type="submit" class="text-xs text-indigo-600 font-semibold hover:underline">
                    Re-translate all in this category
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            @foreach($stats as $key => $stat)
            <div x-show="activeTab === '{{ $key }}'" x-cloak>
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-medium">
                        <tr>
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">Source (EN)</th>
                            <th class="px-6 py-3">Hindi</th>
                            <th class="px-6 py-3">Punjabi</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $items = ($stat['model'])::latest()->paginate(10, ['*'], $key . '_page');
                        @endphp
                        @forelse($items as $item)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 text-sm text-gray-500">#{{ $item->id }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-800">{{ $item->title }}</div>
                                <div class="text-xs text-gray-500 line-clamp-1 mt-0.5">{{ strip_tags($item->description ?? $item->content) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-gray-700 font-devanagari">{{ $item->title_hi ?: '---' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-gray-700 font-gurmukhi">{{ $item->title_pa ?: '---' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @php $status = $item->getTranslationStatus($stat['fields']); @endphp
                                @if($status === 'complete')
                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full uppercase">Complete</span>
                                @elseif($status === 'partial')
                                    <span class="px-2 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full uppercase">Partial</span>
                                @else
                                    <span class="px-2 py-1 bg-rose-100 text-rose-700 text-[10px] font-bold rounded-full uppercase">Missing</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('admin.translations.retranslate') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="model" value="{{ $stat['model'] }}">
                                        <input type="hidden" name="id" value="{{ $item->id }}">
                                        @foreach($stat['fields'] as $f)
                                            <input type="hidden" name="fields[]" value="{{ $f }}">
                                        @endforeach
                                        <button type="submit" title="Auto-translate" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">No records found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4 border-t border-gray-100">
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
