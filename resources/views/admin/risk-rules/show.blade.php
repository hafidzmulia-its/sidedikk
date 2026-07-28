<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-slate-500">Panel Admin SIDEDIKK</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-950">Preview Aturan Risiko</h1>
            </div>
            <a href="{{ route('admin.risk-rules.index') }}" class="sid-button-secondary">Kembali</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @include('admin.partials.nav')

        @if (session('status'))
            <div class="rounded-[24px] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-[var(--color-success)]">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->has('publish'))
            <div class="rounded-[24px] border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-[var(--color-danger)]">
                {{ $errors->first('publish') }}
            </div>
        @endif

        <div class="sid-card p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Versi {{ $version->version_number }}</p>
                    <h2 class="mt-2 text-2xl font-bold text-slate-950">{{ $version->title }}</h2>
                    <p class="mt-2 text-sm text-slate-500">Cakupan skor draft: 0 - {{ $version->max_score_covered }}</p>
                    <p class="mt-1 text-sm text-slate-500">Max skor kuesioner published saat ini: {{ $currentQuestionnaireMaxScore ?? '-' }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <x-admin.partials.status-badge :status="$version->status" />
                    @if ($version->status->value === 'draft')
                        <a href="{{ route('admin.risk-rules.edit', $version) }}" class="sid-button-secondary">Edit Draft</a>
                        <form method="POST" action="{{ route('admin.risk-rules.publish', $version) }}">
                            @csrf
                            <button type="submit" class="sid-button-primary">Publikasikan</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="sid-card p-6">
            <p class="text-lg font-bold text-slate-900">Level Risiko</p>
            <div class="mt-5 space-y-3">
                @foreach ($version->riskLevels as $level)
                    <article class="rounded-[24px] border bg-white p-5">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Urutan {{ $level->display_priority }}</p>
                                <p class="mt-2 text-base font-semibold text-slate-900">{{ $level->name }}</p>
                                <p class="mt-2 text-sm text-slate-500">{{ $level->description }}</p>
                                <p class="mt-2 text-sm text-slate-600">{{ $level->recommendation }}</p>
                            </div>
                            <div class="text-right text-sm text-slate-600">
                                <p>{{ $level->min_score }} - {{ $level->max_score }}</p>
                                <p class="mt-1">{{ $level->semantic_color }}</p>
                                <p class="mt-2 font-semibold">{{ $level->is_active ? 'Aktif' : 'Nonaktif' }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
