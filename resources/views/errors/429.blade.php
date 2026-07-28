<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>429 - Terlalu Banyak Permintaan</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    <div class="sid-shell flex items-center justify-center px-4 py-10">
        <div class="sid-card w-full max-w-xl p-8 text-center">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[var(--color-primary)]">429</p>
            <h1 class="mt-3 text-3xl font-extrabold text-slate-950">Permintaan terlalu sering</h1>
            <p class="mt-4 text-sm leading-7 text-slate-600">Tunggu sebentar, lalu ulangi proses login atau pengiriman screening secara bertahap.</p>
            <div class="mt-6">
                <a href="/" class="sid-button-primary">Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</body>
</html>
