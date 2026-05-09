@extends('layouts.student')

@section('title', __('careers.title') . ' - Nabha Digital Learning')

@section('student-content')
<div class="space-y-6">
    <div class="text-center bg-white rounded-2xl shadow p-8 mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ __('careers.discover_future') }}</h1>
        <p class="text-gray-600 max-w-2xl mx-auto">{{ __('careers.explore_paths') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($careers as $career)
            <a href="{{ route('student.careers.show', $career['id']) }}" class="block group">
                <div class="bg-{{ $career['bg_color'] }}-50 rounded-xl p-6 border border-{{ $career['bg_color'] }}-100 h-full hover:shadow-lg transition-all duration-300 transform group-hover:-translate-y-1">
                    <div class="w-12 h-12 bg-{{ $career['bg_color'] }}-100 text-{{ $career['bg_color'] }}-600 rounded-lg flex items-center justify-center text-2xl mb-4">
                        {{ $career['icon'] }}
                    </div>
                    <h3 class="text-xl font-bold text-{{ $career['bg_color'] }}-900 mb-2">{{ $career['title'] }}</h3>
                    <p class="text-sm text-{{ $career['bg_color'] }}-700 mb-4 h-16 overflow-hidden">
                        {{ $career['description'] }}
                    </p>
                    <ul class="text-sm text-{{ $career['bg_color'] }}-800 space-y-2 mb-4">
                        @foreach(array_slice($career['skills'], 0, 3) as $skill)
                            <li>✓ {{ $skill }}</li>
                        @endforeach
                    </ul>
                    <div class="mt-4 pt-4 border-t border-{{ $career['bg_color'] }}-200">
                        <span class="text-{{ $career['bg_color'] }}-600 font-medium text-sm flex items-center group-hover:text-{{ $career['bg_color'] }}-800">
                            {{ __('careers.view_full_roadmap') }} <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
