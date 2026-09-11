@props([
    'type' => 'primary', // primary, success, warning, danger, neutral
    'size' => 'md', // sm, md
    'rounded' => 'full' // full, md, xl
])

@php
    $typeClasses = match($type) {
        'primary' => 'bg-brand-primary/10 text-brand-primary border-brand-primary/20',
        'success' => 'bg-brand-success/10 text-brand-success border-brand-success/20',
        'warning' => 'bg-brand-warning/10 text-brand-warning border-brand-warning/20',
        'danger' => 'bg-brand-danger/10 text-brand-danger border-brand-danger/20',
        'neutral' => 'bg-gray-100 text-gray-600 border-gray-200',
        default => 'bg-brand-primary/10 text-brand-primary border-brand-primary/20',
    };

    $sizeClasses = match($size) {
        'sm' => 'text-[10px] px-2 py-0.5',
        'md' => 'text-xs px-2.5 py-1',
        default => 'text-xs px-2.5 py-1',
    };

    $roundedClasses = match($rounded) {
        'md' => 'rounded-md',
        'xl' => 'rounded-xl',
        'full' => 'rounded-full',
        default => 'rounded-full',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-medium border {$typeClasses} {$sizeClasses} {$roundedClasses}"]) }}>
    {{ $slot }}
</span>
