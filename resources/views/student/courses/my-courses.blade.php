@extends('layouts.student')

@section('title', 'My Courses - Nabha Learning')

@section('student-content')
<div class="space-y-6 animate-fade-in">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-white/90 tracking-tight" style="font-family: var(--font-display);">{{ __('messages.my_courses') }}</h1>
            <p class="text-xs text-white/40 mt-1">{{ __('messages.course_count', ['count' => $enrollments->count()]) }}</p>
        </div>
        <a href="{{ route('student.courses') }}"
           class="text-xs bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white px-4 py-2.5 rounded-xl transition font-bold uppercase tracking-wider shadow-lg shadow-violet-500/20">
            {{ __('messages.browse_more') }}
        </a>
    </div>

    @if($enrollments->isEmpty())
        <div class="glass-card p-16 text-center glow-violet">
            <h3 class="text-base font-bold text-white/90 mb-2" style="font-family: var(--font-display);">{{ __('messages.no_courses_yet') }}</h3>
            <p class="text-white/40 text-xs mb-6 max-w-xs mx-auto leading-relaxed">{{ __('messages.start_learning_journey') }}</p>
            <a href="{{ route('student.courses') }}"
               class="inline-block bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition-all shadow-lg shadow-violet-500/20">
                {{ __('messages.explore_courses') }}
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($enrollments as $enrollment)
                @php $course = $enrollment->course; @endphp
                @if($course)
                    @php
                        $refundLabels = [
                            'none' => ['label' => 'Active Enrollment', 'class' => 'bg-white/[0.04] text-white/50 border border-white/[0.06]'],
                            'requested' => ['label' => 'Refund requested', 'class' => 'bg-amber-500/15 text-amber-300 border border-amber-500/15'],
                            'partial' => ['label' => 'Partial refund', 'class' => 'bg-blue-500/15 text-blue-300 border border-blue-500/15'],
                            'full' => ['label' => 'Refund completed', 'class' => 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/15'],
                            'rejected' => ['label' => 'Refund rejected', 'class' => 'bg-red-500/15 text-red-300 border border-red-500/15'],
                        ];
                        $refundMeta = $refundLabels[$enrollment->refund_status] ?? $refundLabels['none'];
                    @endphp
                    <div class="group glass-card overflow-hidden hover:border-violet-500/20 transition-all duration-300" x-data="{ showRefundForm: false }">

                        {{-- Thumbnail --}}
                        <a href="{{ route('student.courses.show', $course) }}" class="block relative h-40 overflow-hidden">
                            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>

                            <div class="absolute top-3 right-3">
                                @if($enrollment->payment_status === 'paid')
                                    <span class="bg-amber-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">{{ __('messages.paid') }}</span>
                                @else
                                    <span class="bg-emerald-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">{{ __('messages.free') }}</span>
                                @endif
                            </div>

                            <div class="absolute bottom-3 left-3">
                                <span class="bg-white/10 backdrop-blur-md text-white/90 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider border border-white/10">{{ $course->subject }}</span>
                            </div>
                        </a>

                        <div class="p-5 space-y-4">
                            <a href="{{ route('student.courses.show', $course) }}">
                                <h3 class="font-bold text-white/90 text-sm line-clamp-2 group-hover:text-violet-400 transition" style="font-family: var(--font-display);">
                                    {{ $course->title }}
                                </h3>
                            </a>

                            <div class="flex items-center justify-between text-xs text-white/40">
                                <div class="flex items-center gap-1.5">
                                    <img src="{{ $course->teacher->avatar_url }}" class="w-5 h-5 rounded-md object-cover ring-1 ring-white/10" alt="">
                                    <span>{{ $course->teacher->name }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span>{{ $course->lessons_count }} {{ __('messages.lessons') }}</span>
                                    <span>&middot;</span>
                                    <span>{{ $course->class_level }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-3.5 border-t border-white/[0.06]">
                                <div>
                                    <p class="text-[10px] text-white/35 font-semibold uppercase tracking-wider">{{ __('messages.enrolled') }}</p>
                                    <p class="text-xs font-medium text-white/80 mt-0.5">{{ $enrollment->enrolled_at->format('d M Y') }}</p>
                                </div>

                                @if($course->lessons_count > 0)
                                    <a href="{{ route('student.courses.lesson', [$course, $course->lessons->first()]) }}"
                                       class="text-xs bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white px-4 py-2 rounded-xl font-bold uppercase tracking-wider transition-all shadow-md">
                                        {{ __('messages.continue') }} →
                                    </a>
                                @else
                                    <a href="{{ route('student.courses.show', $course) }}"
                                       class="text-xs bg-white/[0.06] hover:bg-white/[0.1] text-white/85 px-4 py-2 rounded-xl font-bold uppercase tracking-wider transition-all">
                                        {{ __('messages.view') }}
                                    </a>
                                @endif
                            </div>

                            <div class="pt-1 flex items-center justify-between gap-2">
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider {{ $refundMeta['class'] }}">
                                    {{ $refundMeta['label'] }}
                                </span>

                                @if($enrollment->payment_status === 'paid' && $enrollment->refund_status === 'none')
                                    <button type="button"
                                            @click="showRefundForm = !showRefundForm"
                                            class="text-xs font-semibold text-violet-400 hover:text-violet-300 transition">
                                        Request refund
                                    </button>
                                @endif
                            </div>

                            @if($enrollment->payment_status === 'paid' && $enrollment->refund_status === 'none')
                                <div x-show="showRefundForm" x-cloak class="pt-3.5 border-t border-white/[0.06] animate-slide-in">
                                    <form method="POST" action="{{ route('student.courses.refund', $course) }}" class="space-y-3">
                                        @csrf
                                        <div>
                                            <label class="block text-[10px] font-semibold text-white/50 mb-1.5 uppercase tracking-wider">Refund reason</label>
                                            <textarea name="refund_reason" rows="3" required maxlength="1000"
                                                      class="w-full px-4 py-2.5 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-xs resize-none"
                                                      placeholder="Tell us why you are requesting a refund"></textarea>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-white/50 mb-1.5 uppercase tracking-wider">Requested amount</label>
                                            <input type="number" name="refund_amount" min="0.01" max="{{ (float) $enrollment->amount_paid }}" step="0.01"
                                                   class="w-full px-4 py-2.5 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-xs"
                                                   placeholder="{{ number_format((float) $enrollment->amount_paid, 2) }}">
                                        </div>
                                        <button type="submit"
                                                class="w-full bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white text-xs font-bold py-2.5 rounded-xl uppercase tracking-wider transition-all shadow-md">
                                            Submit refund request
                                        </button>
                                    </form>
                                </div>
                            @endif

                            @if($enrollment->transaction_id)
                                <p class="text-[9px] text-white/20 mt-1 font-mono text-center">TXN: {{ $enrollment->transaction_id }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
@endsection
