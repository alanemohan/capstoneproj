@extends('layouts.student')

@section('title', __('messages.mentors') . ' - Nabha Learning')

@section('student-content')
<div class="max-w-4xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Your Academic Mentor</h1>
            <p class="text-gray-500 mt-1">Get personalized guidance and support from your assigned mentor.</p>
        </div>
    </div>

    @if($mentors->count() > 0)
        @foreach($mentors as $mentor)
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 transition-all hover:shadow-2xl">
                <div class="md:flex">
                    <div class="md:w-1/3 bg-gradient-to-br from-primary-600 to-indigo-700 p-8 flex flex-col items-center justify-center text-white">
                        <div class="relative">
                            <img src="{{ $mentor->avatar_url }}" alt="{{ $mentor->name }}" 
                                 class="w-32 h-32 rounded-full border-4 border-white/30 shadow-lg object-cover">
                            <div class="absolute bottom-1 right-1 w-6 h-6 bg-emerald-500 border-4 border-white rounded-full"></div>
                        </div>
                        <h2 class="mt-4 text-xl font-bold text-center">{{ $mentor->name }}</h2>
                        <p class="text-primary-100 text-sm opacity-90">{{ $mentor->subject_specialization ?? 'Academic Expert' }}</p>
                    </div>
                    
                    <div class="md:w-2/3 p-8">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Mentor Profile</h3>
                                <p class="text-sm text-gray-500">Expertise in {{ $mentor->subject_specialization ?? 'General Academics' }}</p>
                            </div>
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full uppercase tracking-wider">
                                Assigned
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-primary-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-medium">Email Address</p>
                                    <p class="text-sm font-semibold text-gray-700">{{ $mentor->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-primary-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-medium">Phone Number</p>
                                    <p class="text-sm font-semibold text-gray-700">{{ $mentor->phone ?? 'Not shared' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-primary-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-medium">Availability</p>
                                    <p class="text-sm font-semibold text-gray-700">{{ $mentor->availability ?? 'Mon - Fri (10 AM - 4 PM)' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-primary-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m4 0h1m-5 10h5m-5 4h5m-4-4v4m1-4v4m1-4v4"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-medium">Affiliation</p>
                                    <p class="text-sm font-semibold text-gray-700">{{ $mentor->school }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-primary-50 rounded-2xl p-4 border border-primary-100 mb-6">
                            <p class="text-sm text-primary-700">
                                <strong>Mentor Guidance:</strong> For any academic queries please contact your assigned mentor. They are here to help you navigate your learning journey and achieve your goals.
                            </p>
                        </div>

                        <div class="flex gap-3">
                            <a href="mailto:{{ $mentor->email }}" 
                               class="flex-1 bg-primary-600 text-white font-bold py-3 px-6 rounded-xl hover:bg-primary-700 transition shadow-lg shadow-primary-200 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                Send Email
                            </a>
                            @if($mentor->phone)
                            <a href="tel:{{ $mentor->phone }}" 
                               class="bg-white border border-gray-200 text-gray-700 font-bold py-3 px-6 rounded-xl hover:bg-gray-50 transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                Call Now
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="bg-white rounded-3xl shadow-lg p-16 text-center border border-dashed border-gray-200">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No mentor assigned yet</h3>
            <p class="text-gray-500 max-w-sm mx-auto">Please wait for the administrator to assign a mentor to your profile. Mentors help you with your academic progress and career roadmap.</p>
        </div>
    @endif
</div>
@endsection
