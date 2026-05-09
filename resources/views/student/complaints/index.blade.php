@extends('layouts.student')

@section('title', __('messages.help_complaints') . ' - Nabha Digital Learning')

@section('student-content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('messages.help_complaints') }}</h1>
    </div>

    <div class="bg-white rounded-xl shadow p-6 md:p-8">
        <h2 class="text-lg font-bold text-gray-800 mb-4">{{ __('messages.submit_request') }}</h2>
        
        @if(session('success'))
            <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('student.complaints.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.subject') }}</label>
                <input type="text" name="subject" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                       placeholder="{{ __('messages.subject_placeholder') }}">
                @error('subject') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.message') }}</label>
                <textarea name="message" rows="4" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                          placeholder="{{ __('messages.message_placeholder') }}"></textarea>
                @error('message') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-6 rounded-lg transition">
                {{ __('messages.submit') }}
            </button>
        </form>
    </div>

    @if($complaints->count() > 0)
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-800">{{ __('messages.previous_requests') }}</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($complaints as $complaint)
                    <div class="p-6 flex flex-col md:flex-row justify-between gap-4">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $complaint->subject }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $complaint->message }}</p>
                            <span class="text-xs text-gray-400 mt-2 block">{{ $complaint->created_at->diffForHumans() }}</span>
                        </div>
                        <div>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $complaint->status === 'resolved' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ ucfirst($complaint->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
