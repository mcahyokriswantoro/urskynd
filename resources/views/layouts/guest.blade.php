<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'URSKYND') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=5">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=5">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-brand-text antialiased bg-brand-cream">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div>
                <a href="/">
                    <x-application-logo class="w-48 h-auto mix-blend-multiply" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-soft-lg overflow-hidden sm:rounded-3xl border border-brand-border/50">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
