<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-brand-primary to-brand-primaryAlt border border-transparent rounded-2xl font-medium text-white shadow-soft hover:shadow-soft-lg hover:from-brand-primaryAlt hover:to-brand-primaryLight focus:outline-none focus:ring-2 focus:ring-brand-primary/50 focus:ring-offset-2 transition-all duration-300 ease-in-out w-full sm:w-auto']) }}>
    {{ $slot }}
</button>
