@props(['title' => null, 'description' => null, 'footer' => null])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden']) }}>
    @if($title || $description)
        <div class="px-5 py-4 border-b border-gray-100">
            @if($title)
                <h3 class="font-semibold text-gray-800">{{ $title }}</h3>
            @endif
            @if($description)
                <p class="text-sm text-gray-500 mt-1">{{ $description }}</p>
            @endif
        </div>
    @endif

    <div class="p-5">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
            {{ $footer }}
        </div>
    @endif
</div>
