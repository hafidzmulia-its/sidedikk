@props(['status'])

@php
    $value = $status instanceof \BackedEnum ? $status->value : (string) $status;

    $classes = match ($value) {
        'published' => 'bg-emerald-50 text-[var(--color-success)]',
        'draft' => 'bg-amber-50 text-[var(--color-warning)]',
        'archived', 'unpublished' => 'bg-slate-100 text-slate-600',
        'completed' => 'bg-emerald-50 text-[var(--color-success)]',
        'in_progress' => 'bg-sky-50 text-[var(--color-info)]',
        default => 'bg-[var(--color-primary-soft)] text-[var(--color-primary)]',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex rounded-full px-3 py-1 text-xs font-semibold {$classes}"]) }}>
    {{ str($value)->replace('_', ' ')->title() }}
</span>
