@props([
    'title',
    'eyebrow' => 'Panel Admin SIDEDIKK',
    'description' => null,
])

<div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div class="min-w-0">
        <p class="text-sm font-medium text-slate-500">{{ $eyebrow }}</p>
        <h1 class="mt-1 text-[2rem] font-semibold leading-tight text-slate-950 sm:text-[2.25rem] sm:font-extrabold sm:leading-[1.05] sm:tracking-tight">
            {{ $title }}
        </h1>
        @if ($description)
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">{{ $description }}</p>
        @endif
    </div>

    @if (trim((string) $slot) !== '')
        <div class="flex flex-wrap gap-3">
            {{ $slot }}
        </div>
    @endif
</div>
