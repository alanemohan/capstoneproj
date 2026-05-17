@props(['title' => null, 'description' => null, 'footer' => null])

{{-- Auto-detect portal context and apply appropriate styling --}}
@php
    $isStudent = request()->routeIs('student.*');
    $baseClass = $isStudent
        ? 'glass-card overflow-hidden'
        : 'bg-white rounded-xl border border-gray-200 overflow-hidden';
    $headerClass = $isStudent
        ? 'px-5 py-3.5 border-b border-white/[0.06]'
        : 'px-5 py-3.5 border-b border-gray-100';
    $titleClass = $isStudent
        ? 'font-semibold text-white/80 text-sm'
        : 'font-semibold text-gray-800 text-sm';
    $descClass = $isStudent
        ? 'text-xs text-white/40 mt-1'
        : 'text-xs text-gray-500 mt-1';
    $bodyClass = 'p-5';
    $footerClass = $isStudent
        ? 'px-5 py-3 bg-white/[0.02] border-t border-white/[0.06]'
        : 'px-5 py-3 bg-gray-50 border-t border-gray-100';
@endphp

<div {{ $attributes->merge(['class' => $baseClass]) }}>
    @if($title || $description)
        <div class="{{ $headerClass }}">
            @if($title)
                <h3 class="{{ $titleClass }}">{{ $title }}</h3>
            @endif
            @if($description)
                <p class="{{ $descClass }}">{{ $description }}</p>
            @endif
        </div>
    @endif

    <div class="{{ $bodyClass }}">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="{{ $footerClass }}">
            {{ $footer }}
        </div>
    @endif
</div>
