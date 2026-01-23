<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="manifest" href="/manifest.webmanifest">
        <meta name="theme-color" content="#f59e0b">

        <link rel="apple-touch-icon" href="/icons/icon-192.png">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">


        <title>{{ config('app.name', 'JF Instalações') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <script>
    if ("serviceWorker" in navigator) {
        window.addEventListener("load", () => {
        navigator.serviceWorker.register("/sw.js");
        });
    }
    </script>


    <body class="font-sans antialiased bg-[#071827] text-gray-900">
        <div class="min-h-screen bg-[#071827]">
            {{-- Navigation (Breeze) --}}
            @include('layouts.navigation')

            {{-- Page Heading --}}
            @isset($header)
                <header class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                    <div class="bg-white/95 backdrop-blur shadow-xl rounded-2xl px-6 py-5 border border-white/20">
                        <div class="text-[#071827] font-extrabold text-xl">
                            {{ $header }}
                        </div>
                    </div>
                </header>
            @endisset

            {{-- Page Content --}}
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="bg-white/95 backdrop-blur shadow-xl rounded-2xl p-6 border border-white/20">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
