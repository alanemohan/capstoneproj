@extends('layouts.student')

@section('title', __('messages.mentors') . ' - ' . __('messages.platform_name'))

@section('student-content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('messages.mentors_title') }}</h1>
    </div>

    @if($mentors->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($mentors as $mentor)
                <div class="bg-white rounded-xl shadow-md p-6 flex flex-col items-center text-center">
                    <img src="{{ $mentor->avatar_url }}" alt="{{ $mentor->name }}" class="w-24 h-24 rounded-full mb-4 border-4 border-primary-50">
                    <h3 class="text-lg font-bold text-gray-900">{{ $mentor->name }}</h3>
                    <p class="text-sm text-primary-600 font-medium mb-2">{{ $mentor->subject_specialization ?? __('messages.general_mentor') }}</p>
                    <p class="text-xs text-gray-500 mb-4">{{ $mentor->school }}</p>
                    
                    <a href="mailto:{{ $mentor->email }}" class="mt-auto bg-primary-50 text-primary-700 font-medium py-2 px-6 rounded-lg hover:bg-primary-100 transition w-full">
                        {{ __('messages.contact_email') }}
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl shadow p-12 text-center text-gray-500">
            <p>{{ __('messages.no_mentors') }}</p>
        </div>
    @endif
</div>
@endsection
