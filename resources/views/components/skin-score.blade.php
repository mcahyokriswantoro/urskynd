@props(['score' => 0, 'size' => 'md'])

@php
    $sizeClasses = match($size) {
        'sm' => 'text-2xl',
        'md' => 'text-4xl md:text-5xl',
        'lg' => 'text-6xl md:text-7xl',
        default => 'text-4xl md:text-5xl',
    };

    $colorClasses = match(true) {
        $score >= 80 => 'text-brand-success',
        $score >= 60 => 'text-brand-primary',
        $score >= 40 => 'text-brand-warning',
        default => 'text-brand-danger',
    };
@endphp

<div class="flex items-baseline gap-1">
    <span class="font-serif font-bold {{ $sizeClasses }} {{ $colorClasses }}">
        {{ $score }}
    </span>
    <span class="text-brand-textAlt font-medium {{ $size === 'lg' ? 'text-xl' : 'text-sm' }}">
        /100
    </span>
</div>
