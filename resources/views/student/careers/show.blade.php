@extends('layouts.student')

@section('title', $career['title'] . ' - ' . __('careers.title'))

@section('student-content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="mb-4">
        <a href="{{ route('student.careers') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>{{ __('careers.back_to_roadmaps') }}</span>
        </a>
    </div>

    <!-- Header Section -->
    <div class="bg-{{ $career['bg_color'] }}-50 rounded-2xl shadow-sm border border-{{ $career['bg_color'] }}-100 overflow-hidden">
        <div class="p-8 md:p-12 text-center md:text-left flex flex-col md:flex-row items-center gap-8">
            <div class="w-24 h-24 bg-white rounded-2xl shadow flex items-center justify-center text-5xl flex-shrink-0 border-2 border-{{ $career['bg_color'] }}-200">
                {{ $career['icon'] }}
            </div>
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-{{ $career['bg_color'] }}-900 mb-4">{{ $career['title'] }}</h1>
                <p class="text-lg text-{{ $career['bg_color'] }}-800 max-w-2xl">{{ $career['description'] }}</p>
                <div class="mt-6 inline-flex items-center px-4 py-2 bg-white rounded-lg border border-{{ $career['bg_color'] }}-200 text-{{ $career['bg_color'] }}-800 font-semibold shadow-sm">
                    <svg class="w-5 h-5 mr-2 text-{{ $career['bg_color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ __('careers.salary') }}: {{ $career['salary_range'] }}
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Skills Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                {{ __('careers.skills') }}
            </h2>
            <div class="flex flex-wrap gap-2">
                @foreach($career['skills'] as $skill)
                    <span class="bg-gray-100 text-gray-800 px-3 py-1.5 rounded-lg text-sm font-medium border border-gray-200">
                        {{ $skill }}
                    </span>
                @endforeach
            </div>
        </div>

        <!-- Roadmap Timeline -->
        <div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-8 flex items-center">
                <svg class="w-7 h-7 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                {{ __('careers.roadmap') }}
            </h2>
            
            <div class="space-y-8 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-indigo-500 before:via-purple-500 before:to-gray-200">
                @foreach($career['roadmap'] as $index => $step)
                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white bg-indigo-500 text-white font-bold shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10">
                            {{ $index + 1 }}
                        </div>
                        <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 rounded-xl border border-gray-200 bg-white shadow-sm transition-all hover:border-indigo-300 hover:shadow-md">
                            <h3 class="font-bold text-gray-900 text-lg mb-1">{{ __('messages.step') }} {{ $index + 1 }}</h3>
                            <p class="text-gray-600 leading-snug">{{ $step }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
