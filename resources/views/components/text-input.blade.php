@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-brand-border bg-white text-brand-text focus:border-brand-primary focus:ring-brand-primary/30 rounded-xl shadow-sm placeholder-brand-secondary py-3 px-4 w-full transition-all duration-200 ease-in-out']) }}>
