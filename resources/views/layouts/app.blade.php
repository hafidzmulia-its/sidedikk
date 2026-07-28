<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @php($authUser = auth()->user())
    @php($isUserPanel = $authUser?->role === \App\Enums\UserRole::User)
    @php($isAdminPanel = $authUser?->role === \App\Enums\UserRole::Admin)
    @php($showGlobalPanelNav = $isAdminPanel || ($isUserPanel && request()->routeIs('dashboard')))
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <body class="{{ $isUserPanel ? 'sid-mobile-preview' : '' }}">
        <div class="sid-shell {{ $isUserPanel ? 'sid-mobile-container' : '' }}">
            @if ($showGlobalPanelNav)
                @include('layouts.navigation')
            @endif

            <main class="{{ $isUserPanel ? 'pb-24' : 'sid-container py-6 pb-24 md:pb-6' }}">
                @isset($header)
                    {{ $header }}
                @endisset

                {{ $slot }}
            </main>

            @if ($isAdminPanel)
                @include('layouts.admin-bottom-nav')
            @endif

            @if ($isUserPanel)
                @include('layouts.user-bottom-nav')
            @endif
        </div>
    </body>
</html>
