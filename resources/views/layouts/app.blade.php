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
    <body class="font-sans antialiased text-brand-text bg-brand-cream">
        <div class="min-h-screen flex flex-col md:flex-row">
            
            <!-- Desktop Sidebar -->
            <x-sidebar />

            <!-- Main Content Area -->
            <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
                
                <!-- Desktop Top Navigation -->
                <x-top-navigation />

                <!-- Page Content -->
                <main class="flex-1 px-4 py-6 md:p-8 max-w-7xl mx-auto w-full pb-24 md:pb-8">
                    {{ $slot }}
                </main>
            </div>

            <!-- Mobile Bottom Navigation -->
            <x-bottom-navigation />
            
        </div>
    </body>
</html>
