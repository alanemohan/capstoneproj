@props([
    'variant' => 'info', // success, warning, danger, info, gray
])

@php
    $isStudent = request()->routeIs('student.*');
    $baseClasses = 'inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold';

    if ($isStudent) {
        $variantClasses = match($variant) {
            'success' => 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/15',
            'warning' => 'bg-amber-500/15 text-amber-400 border border-amber-500/15',
            'danger' => 'bg-red-500/15 text-red-400 border border-red-500/15',
            'info' => 'bg-violet-500/15 text-violet-400 border border-violet-500/15',
            'primary' => 'bg-cyan-500/15 text-cyan-400 border border-cyan-500/15',
            default => 'bg-white/[0.06] text-white/50 border border-white/[0.08]',
        };
    } else {
        $variantClasses = match($variant) {
            'success' => 'bg-emerald-50 text-emerald-700',
            'warning' => 'bg-yellow-50 text-yellow-700',
            'danger' => 'bg-red-50 text-red-700',
            'info' => 'bg-blue-50 text-blue-700',
            'primary' => 'bg-indigo-50 text-indigo-700',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    $classes = "{$baseClasses} {$variantClasses}";
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
