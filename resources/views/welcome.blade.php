<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#9C36B5">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="SIDEDIKK">
        <link rel="manifest" href="/manifest.webmanifest">
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('brand/icon-192.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('brand/icon-192.png') }}">
        <title>{{ config('app.name', 'SIDEDIKK') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="sid-mobile-preview">
        <main class="sid-mobile-container relative flex min-h-[97vh] flex-col items-center justify-between overflow-hidden px-5 py-8">
            <div class="flex w-full flex-col items-center space-y-4 pt-2 text-center">
                <div class="relative h-32 w-32">
                    <div class="absolute inset-0 flex items-center justify-center rounded-full bg-[var(--color-primary-soft)] shadow-[0_4px_24px_rgba(149,64,158,0.15)]">
                        <img src="{{ asset('brand/icon-512.png') }}" alt="SIDEDIKK Logo" class="h-full w-full rounded-full object-cover">
                    </div>
                </div>

                <div class="space-y-2">
                    <h1 class="text-[28px] font-bold tracking-tight text-[var(--color-primary)]">SIDEDIKK</h1>
                    <h2 class="text-sm font-semibold text-[var(--color-primary)]">Deteksi Dini Komplikasi Kehamilan</h2>
                    <p class="mx-auto max-w-[280px] text-xs font-normal leading-5 text-[#50434e]">
                        Kenali risiko sejak dini, untuk kehamilan yang lebih sehat dan aman.
                    </p>
                </div>
            </div>

            <div class="relative flex flex-1 items-center justify-center py-8">
                <div class="flex aspect-square w-full max-w-[280px] items-center justify-center overflow-hidden rounded-full border-2 border-white bg-white shadow-[0_8px_32px_rgba(149,64,158,0.08)]">
                    <img src="{{ asset('assets/welcome.png') }}" alt="SIDEDIKK" class="h-52 w-52 rounded-full object-cover">
                </div>
                <div class="absolute right-6 top-1/4 h-6 w-6 rounded-full bg-[var(--color-primary-soft)]"></div>
                <div class="absolute bottom-1/3 left-6 h-4 w-4 rounded-full bg-[#fcaaff]"></div>
                <div class="absolute left-10 top-1/2 h-8 w-8 rounded-full border border-[#eedaf1]"></div>
            </div>

            <div class="w-3/4 space-y-3 pb-4">
                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="sid-button-primary flex h-14 w-full items-center justify-center gap-2 text-sm font-semibold">
                    Masuk SIDEDIKK
                    <span class="material-symbols-outlined text-xl">arrow_forward</span>
                </a>
                <button type="button" data-pwa-install class="sid-button-secondary hidden h-12 w-full text-sm font-semibold">
                    Pasang Aplikasi
                </button>
            </div>
        </main>
    </body>
</html>
