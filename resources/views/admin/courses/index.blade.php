@extends('layouts.admin')

@section('title', 'Course Approval - Nabha Learning')

@section('admin-content')
<div class="space-y-6 animate-fade-in text-slate-800">
    <div class="pb-5 border-b border-slate-200">
        <h1 class="text-xl font-bold text-slate-900 tracking-tight" style="font-family: var(--font-display);">Course Approval</h1>
        <p class="text-xs text-slate-500 mt-1 font-semibold">Review and approve courses submitted by teachers.</p>
    </div>

    {{-- Filter tabs --}}
    <div class="flex gap-2 flex-wrap">
        @foreach(['' => 'All', 'pending' => 'Pending', 'published' => 'Approved', 'rejected' => 'Rejected', 'draft' => 'Draft'] as $val => $label)
            <a href="{{ route('admin.courses', ['status' => $val]) }}"
               class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition border border-transparent shadow-sm
                      {{ request('status', '') === $val ? 'bg-orange-500 border-orange-500 text-white' : 'bg-white border-slate-350 text-slate-650 hover:bg-slate-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if($courses->isEmpty())
        <div class="bg-white rounded-xl p-12 text-center border border-slate-200 text-xs text-slate-400 font-semibold shadow-sm">
            <p>No courses to review.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($courses as $course)
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:border-orange-500/10 transition">
                    <div class="flex flex-col md:flex-row md:items-start gap-4">
                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                             class="w-24 h-16 object-cover rounded-lg flex-shrink-0 border border-slate-200">

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1.5">
                                <h3 class="font-bold text-slate-900 text-sm" style="font-family: var(--font-display);">{{ $course->title }}</h3>
                                @php 
                                    $sc = [
                                        'draft' => 'bg-slate-50 border-slate-200 text-slate-600',
                                        'pending' => 'bg-yellow-50 border-yellow-250 text-yellow-750',
                                        'published' => 'bg-emerald-50 border-emerald-250 text-emerald-700',
                                        'rejected' => 'bg-red-50 border-red-250 text-red-700'
                                    ]; 
                                @endphp
                                <span class="text-[9px] font-bold px-2.5 py-0.5 border rounded-md uppercase tracking-wider {{ $sc[$course->status] ?? '' }}">
                                    {{ $course->status }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-550 leading-relaxed line-clamp-2">{{ $course->description }}</p>
                            <div class="flex flex-wrap items-center gap-2.5 mt-3.5 text-[9px] font-bold uppercase tracking-wider text-slate-400">
                                <span class="text-slate-600 font-bold">{{ $course->teacher->name }}</span>
                                <span>•</span>
                                <span>{{ $course->lessons_count }} lessons</span>
                                <span>•</span>
                                <span>{{ $course->enrollments_count }} enrolled</span>
                                <span>•</span>
                                <span>{{ $course->class_level }} | {{ $course->subject }}</span>
                                <span>•</span>
                                <span class="text-emerald-700">{{ $course->price > 0 ? '₹' . number_format($course->price, 2) : 'Free' }}</span>
                                <span>•</span>
                                <span class="normal-case font-semibold text-[10px]">{{ $course->created_at->format('d M Y') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ route('admin.courses.preview', $course) }}"
                               class="text-[10px] font-bold uppercase tracking-wider bg-orange-50 border border-orange-200 hover:bg-orange-100 text-orange-700 px-3 py-2 rounded-md transition shadow-sm">
                                Preview
                            </a>
                            @if($course->status !== 'published')
                                <form method="POST" action="{{ route('admin.courses.approve', $course) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-[10px] font-bold uppercase tracking-wider bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-700 px-3.5 py-2 rounded-md transition shadow-sm">
                                        Approve
                                    </button>
                                </form>
                            @endif
                            @if($course->status !== 'rejected')
                                <form method="POST" action="{{ route('admin.courses.reject', $course) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-[10px] font-bold uppercase tracking-wider bg-amber-50 border border-amber-200 hover:bg-amber-100 text-amber-700 px-3.5 py-2 rounded-md transition shadow-sm">
                                        Reject
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                                  onsubmit="return confirm('Permanently delete this course?')" class="inline">
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
        <div class="pt-3">{{ $courses->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
