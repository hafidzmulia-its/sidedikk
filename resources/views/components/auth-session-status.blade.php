@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-2xl border border-[var(--color-info)]/20 bg-[var(--color-info)]/10 px-4 py-3 text-sm font-medium text-[var(--color-info)]']) }}>
        {{ $status }}
    </div>
@endif
