@extends('layouts.admin')

@section('title', 'Content Review - Nabha Learning')

@section('admin-content')
<div class="space-y-6 animate-fade-in">
    <div class="pb-5 border-b border-slate-200">
        <h1 class="text-xl font-bold text-slate-900 tracking-tight" style="font-family: var(--font-display);">Content Review</h1>
        <p class="text-xs text-slate-500 mt-1 font-semibold">Review and approve lessons submitted by teachers.</p>
    </div>

    <!-- Filter Tabs -->
    <div class="flex gap-2 flex-wrap">
        @php $statuses = ['' => 'All', 'pending' => 'Pending', 'published' => 'Approved', 'rejected' => 'Rejected']; @endphp
        @foreach($statuses as $value => $label)
            <a href="{{ route('admin.content', ['status' => $value]) }}"
               class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition border border-transparent shadow-sm
                      {{ request('status', '') === $value
                         ? 'bg-orange-500 border-orange-500 text-white'
                         : 'bg-white border-slate-300 text-slate-600 hover:bg-slate-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if($lessons->isEmpty())
        <div class="bg-white rounded-xl p-12 text-center border border-slate-200 text-xs text-slate-400 font-semibold shadow-sm">
            <p>No lessons to review. Everything is up to date.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($lessons as $lesson)
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:border-orange-500/10 transition">
                    <div class="flex flex-col md:flex-row md:items-start gap-4">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1.5">
                                    <h3 class="font-bold text-slate-900 text-sm" style="font-family: var(--font-display);">{{ $lesson->title }}</h3>
                                    <span class="text-[9px] font-bold px-2.5 py-0.5 border rounded-md uppercase tracking-wider
                                        {{ $lesson->status === 'published' ? 'bg-emerald-50 border-emerald-250 text-emerald-700' : ($lesson->status === 'pending' ? 'bg-yellow-50 border-yellow-250 text-yellow-750' : 'bg-red-50 border-red-250 text-red-700') }}">
                                        {{ $lesson->status }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-550 leading-relaxed line-clamp-2">{{ $lesson->description }}</p>
                                <div class="flex flex-wrap items-center gap-2.5 mt-3.5 text-[9px] font-bold uppercase tracking-wider text-slate-400">
                                    <span class="text-slate-600">{{ $lesson->teacher->name }}</span>
                                    <span>•</span>
                                    <span>{{ $lesson->subject }}</span>
                                    <span>•</span>
                                    <span>{{ $lesson->class_level }}</span>
                                    <span>•</span>
                                    <span class="normal-case font-semibold text-[10px]">{{ $lesson->created_at->format('d M Y') }}</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-3 mt-2 text-[9px] font-bold uppercase tracking-wider text-slate-400">
                                    <span>{{ $lesson->view_count }} views</span>
                                    <span>{{ $lesson->download_count }} downloads</span>
                                    <span>{{ $lesson->quizzes_count }} quizzes</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ route('admin.content.preview', $lesson->id) }}"
                               class="text-[10px] font-bold uppercase tracking-wider bg-orange-50 border border-orange-200 hover:bg-orange-100 text-orange-700 px-3 py-2 rounded-md transition shadow-sm">
                                Preview
                            </a>
                            @if($lesson->status !== 'published')
                                <form method="POST" action="{{ route('admin.content.approve', $lesson->id) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-[10px] font-bold uppercase tracking-wider bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-700 px-3.5 py-2 rounded-md transition shadow-sm">
                                        Approve
                                    </button>
                                </form>
                            @endif
                            @if($lesson->status !== 'rejected')
                                <form method="POST" action="{{ route('admin.content.reject', $lesson->id) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-[10px] font-bold uppercase tracking-wider bg-amber-50 border border-amber-200 hover:bg-amber-100 text-amber-700 px-3.5 py-2 rounded-md transition shadow-sm">
                                        Reject
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.content.destroy', $lesson->id) }}"
                                  onsubmit="return confirm('Permanently delete this lesson?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-[10px] font-bold uppercase tracking-wider bg-red-50 border border-red-200 hover:bg-red-100 text-red-700 px-3 py-2 rounded-md transition shadow-sm">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-3">{{ $lessons->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
