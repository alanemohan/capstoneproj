@extends('layouts.student')

@section('title', __('messages.scholarships') . ' - ' . __('messages.platform_name'))

@section('student-content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('messages.scholarships_title') }}</h1>
    </div>

    @if($scholarships->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($scholarships as $scholarship)
                <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-primary-500 hover:shadow-lg transition">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $scholarship->getTranslated('title') }}</h3>
                    <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ $scholarship->getTranslated('description') }}</p>
                    <div class="space-y-2 mb-6">
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="font-medium w-24">{{ __('messages.amount') }}:</span>
                            <span class="text-emerald-600 font-semibold">{{ $scholarship->getTranslated('amount') ?? __('messages.variable') }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="font-medium w-24">{{ __('messages.deadline') }}:</span>
                            <span class="text-red-500 font-medium">{{ $scholarship->deadline ? \Carbon\Carbon::parse($scholarship->deadline)->format('d M Y') : __('messages.ongoing') }}</span>
                        </div>
                    </div>
                    @if($scholarship->url)
                        <a href="{{ $scholarship->url }}" target="_blank" class="block w-full text-center bg-primary-50 text-primary-700 font-medium py-2 rounded-lg hover:bg-primary-100 transition">
                            {{ __('messages.apply_now') }}
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl shadow p-12 text-center text-gray-500">
            <p>{{ __('messages.no_scholarships') }}</p>
        </div>
    @endif
</div>
@endsection
