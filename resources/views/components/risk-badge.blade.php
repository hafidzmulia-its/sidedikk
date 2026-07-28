@props([
    'label',
    'size' => 'md',
])

@php
    $normalizedLabel = \Illuminate\Support\Str::lower((string) $label);

    $toneClasses = match (true) {
        str_contains($normalizedLabel, 'rendah') => 'bg-emerald-50 text-[#238a57]',
        str_contains($normalizedLabel, 'sedang') => 'bg-amber-50 text-[#a96900]',
        default => 'bg-rose-50 text-[#c83e50]',
    };

    $sizeClasses = match ($size) {
        'sm' => 'px-3 py-1 text-xs font-semibold',
        'lg' => 'px-4 py-2 text-sm font-semibold',
        default => 'px-4 py-2 text-sm font-semibold',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-full {$toneClasses} {$sizeClasses}"]) }}>
    {{ $label }}
</span>
