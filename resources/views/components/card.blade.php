@props(['padding' => 'p-6'])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl shadow-soft border border-brand-creamAlt overflow-hidden']) }}>
    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>
