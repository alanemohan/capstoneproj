@props([
    'variant' => 'info', // success, warning, danger, info, gray
])

@php
    $baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
    
    $variantClasses = match($variant) {
        'success' => 'bg-emerald-100 text-emerald-800',
        'warning' => 'bg-yellow-100 text-yellow-800',
        'danger' => 'bg-red-100 text-red-800',
        'info' => 'bg-blue-100 text-blue-800',
        'primary' => 'bg-primary-100 text-primary-800',
        default => 'bg-gray-100 text-gray-800', // gray
    };

    $classes = "{$baseClasses} {$variantClasses}";
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
