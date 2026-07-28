@props([
    'message' => 'Konten ini masih menggunakan data simulasi untuk pengembangan dan belum menjadi acuan medis final.',
])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs font-medium leading-5 text-[var(--color-warning)]']) }}>
    <div class="flex items-start gap-2">
        <span class="material-symbols-outlined text-[18px]">info</span>
        <p>{{ $message }}</p>
    </div>
</div>
