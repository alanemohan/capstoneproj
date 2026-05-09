@extends('layouts.student')

@section('title', 'My Courses - Nabha Learning')

@section('student-content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('messages.my_courses') }}</h1>
            <p class="text-gray-500 text-sm mt-1">{{ __('messages.course_count', ['count' => $enrollments->count()]) }}</p>
        </div>
        <a href="{{ route('student.courses') }}"
           class="text-sm bg-indigo-600 text-white px-4 py-2.5 rounded-xl hover:bg-indigo-700 transition font-medium">
            {{ __('messages.browse_more') }}
        </a>
    </div>

    @if($enrollments->isEmpty())
        <div class="bg-white rounded-2xl p-16 text-center shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-700 mb-2">{{ __('messages.no_courses_yet') }}</h3>
            <p class="text-gray-400 text-sm mb-6">{{ __('messages.start_learning_journey') }}</p>
            <a href="{{ route('student.courses') }}"
               class="bg-indigo-600 text-white px-6 py-3 rounded-xl hover:bg-indigo-700 transition font-semibold">
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
                            'none' => ['label' => 'No refund requested', 'class' => 'bg-gray-100 text-gray-600'],
                            'requested' => ['label' => 'Refund requested', 'class' => 'bg-amber-100 text-amber-700'],
                            'partial' => ['label' => 'Partial refund', 'class' => 'bg-blue-100 text-blue-700'],
                            'full' => ['label' => 'Refund completed', 'class' => 'bg-green-100 text-green-700'],
                            'rejected' => ['label' => 'Refund rejected', 'class' => 'bg-red-100 text-red-700'],
                        ];
                        $refundMeta = $refundLabels[$enrollment->refund_status] ?? $refundLabels['none'];
                    @endphp
                    <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all duration-200" x-data="{ showRefundForm: false }">

                        {{-- Thumbnail --}}
                        <a href="{{ route('student.courses.show', $course) }}" class="block relative h-40 overflow-hidden">
                            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>

                            <div class="absolute top-3 right-3">
                                @if($enrollment->payment_status === 'paid')
                                    <span class="bg-amber-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">{{ __('messages.paid') }}</span>
                                @else
                                    <span class="bg-emerald-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">{{ __('messages.free') }}</span>
                                @endif
                            </div>

                            <div class="absolute bottom-3 left-3">
                                <span class="bg-white/90 text-gray-700 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $course->subject }}</span>
                            </div>
                        </a>

                        <div class="p-4">
                            <a href="{{ route('student.courses.show', $course) }}">
                                <h3 class="font-bold text-gray-800 text-sm line-clamp-2 group-hover:text-indigo-700 transition">
                                    {{ $course->title }}
                                </h3>
                            </a>

                            <div class="flex items-center gap-2 mt-2">
                                <img src="{{ $course->teacher->avatar_url }}" class="w-4 h-4 rounded-full object-cover" alt="">
                                <span class="text-xs text-gray-400">{{ $course->teacher->name }}</span>
                            </div>

                            <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                                <span>{{ $course->lessons_count }} {{ __('messages.lessons') }}</span>
                                <span>•</span>
                                <span>{{ $course->class_level }}</span>
                            </div>

                            <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                                <div>
                                    <p class="text-xs text-gray-400">{{ __('messages.enrolled') }}</p>
                                    <p class="text-xs font-medium text-gray-600">{{ $enrollment->enrolled_at->format('d M Y') }}</p>
                                </div>

                                @if($course->lessons_count > 0)
                                    <a href="{{ route('student.courses.lesson', [$course, $course->lessons->first()]) }}"
                                       class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 rounded-lg font-semibold transition">
                                        {{ __('messages.continue') }} →
                                    </a>
                                @else
                                    <a href="{{ route('student.courses.show', $course) }}"
                                       class="text-xs bg-gray-100 text-gray-600 px-4 py-1.5 rounded-lg font-medium hover:bg-gray-200 transition">
                                        {{ __('messages.view') }}
                                    </a>
                                @endif
                            </div>

                            <div class="mt-3 flex items-center justify-between gap-2">
                                <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full {{ $refundMeta['class'] }}">
                                    {{ $refundMeta['label'] }}
                                </span>

                                @if($enrollment->payment_status === 'paid' && $enrollment->refund_status === 'none')
                                    <button type="button"
                                            @click="showRefundForm = !showRefundForm"
                                            class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                                        Request refund
                                    </button>
                                @endif
                            </div>

                            @if($enrollment->payment_status === 'paid' && $enrollment->refund_status === 'none')
                                <div x-show="showRefundForm" x-cloak class="mt-3 border-t border-gray-100 pt-3">
                                    <form method="POST" action="{{ route('student.courses.refund', $course) }}" class="space-y-3">
                                        @csrf
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Refund reason</label>
                                            <textarea name="refund_reason" rows="3" required maxlength="1000"
                                                      class="w-full rounded-xl border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                                      placeholder="Tell us why you are requesting a refund"></textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Requested amount</label>
                                            <input type="number" name="refund_amount" min="0.01" max="{{ (float) $enrollment->amount_paid }}" step="0.01"
                                                   class="w-full rounded-xl border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                                   placeholder="{{ number_format((float) $enrollment->amount_paid, 2) }}">
                                        </div>
                                        <button type="submit"
                                                class="w-full bg-indigo-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl hover:bg-indigo-700 transition">
                                            Submit refund request
                                        </button>
                                    </form>
                                </div>
                            @endif

                            @if($enrollment->transaction_id)
                                <p class="text-xs text-gray-300 mt-2 font-mono">TXN: {{ $enrollment->transaction_id }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
@endsection
