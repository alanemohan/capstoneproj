@extends('layouts.student')

@section('title', __('messages.schemes') . ' - ' . __('messages.platform_name'))

@section('student-content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('messages.schemes_title') }}</h1>
    </div>

    @if($schemes->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($schemes as $scheme)
                <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-amber-500 hover:shadow-lg transition">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $scheme->getTranslated('title') }}</h3>
                    <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ $scheme->getTranslated('description') }}</p>
                    <div class="space-y-2 mb-6 text-sm text-gray-600 bg-amber-50 p-3 rounded-lg">
                        <p><span class="font-semibold text-gray-800">{{ __('messages.target') }}:</span> {{ $scheme->getTranslated('target_audience') ?? __('messages.general') }}</p>
                        <p><span class="font-semibold text-gray-800">{{ __('messages.benefits') }}:</span> {{ $scheme->getTranslated('benefits') ?? __('messages.not_available') }}</p>
                    </div>
                    @if($scheme->url)
                        <a href="{{ $scheme->url }}" target="_blank" class="block w-full text-center bg-gray-100 text-gray-700 font-medium py-2 rounded-lg hover:bg-gray-200 transition">
                            {{ __('messages.learn_more') }}
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl shadow p-12 text-center text-gray-500">
            <p>{{ __('messages.no_schemes') }}</p>
        </div>
    @endif
</div>
@endsection
