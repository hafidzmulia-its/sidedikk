@php($riskLabel = \Illuminate\Support\Str::lower($screening->risk_label_snapshot ?? ''))

<x-app-layout>
    <x-slot name="header">
        <header class="sid-app-bar border-b border-[#efddef]">
            <a href="{{ route('history.index') }}" class="sid-top-icon" aria-label="Kembali">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <h1 class="flex-1 pr-10 text-center text-[18px] font-semibold text-[#95409e]">Detail Hasil</h1>
        </header>
    </x-slot>

    <main class="space-y-5 px-5 pb-8 pt-6">
        <section class="sid-card p-5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ $screening->completed_at?->translatedFormat('d F Y H:i') }}</p>
                    <h2 class="mt-2 text-[28px] font-bold text-slate-950">{{ $screening->total_score }}/{{ $screening->max_score }}</h2>
                    <p class="mt-2 text-sm text-slate-500">
                        Usia kehamilan saat screening: {{ $screening->gestational_age_weeks_snapshot }} minggu {{ $screening->gestational_age_days_snapshot }} hari
                    </p>
                </div>
                <div class="rounded-[24px] px-5 py-4 text-center
                    @if (str_contains($riskLabel, 'rendah')) bg-emerald-50 text-[var(--color-success)]
                    @elseif (str_contains($riskLabel, 'sedang')) bg-amber-50 text-[var(--color-warning)]
                    @else bg-rose-50 text-[var(--color-danger)] @endif">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em]">Kategori</p>
                    <p class="mt-2 text-xl font-extrabold">{{ $screening->risk_label_snapshot }}</p>
                </div>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <div class="rounded-[24px] border bg-white p-5">
                    <p class="text-sm font-semibold text-slate-500">Versi Kuesioner</p>
                    <p class="mt-2 text-base font-bold text-slate-900">{{ $screening->display_questionnaire_version_name }}</p>
                </div>
                <div class="rounded-[24px] border bg-white p-5">
                    <p class="text-sm font-semibold text-slate-500">Versi Aturan Risiko</p>
                    <p class="mt-2 text-base font-bold text-slate-900">{{ $screening->display_risk_rule_version_name }}</p>
                </div>
            </div>
        </section>

        <x-medical-disclaimer />

        <section class="sid-card p-6">
            <h2 class="text-xl font-bold text-slate-950">Detail Jawaban</h2>
            <div class="mt-5 space-y-3">
                @foreach ($screening->answers as $answer)
                    <article class="rounded-[24px] border bg-white p-5">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Pertanyaan {{ $answer->display_order_snapshot }}</p>
                                <p class="mt-2 text-base font-semibold text-slate-900">{{ $answer->question_text_snapshot }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex rounded-full bg-[var(--color-primary-soft)] px-3 py-1 text-xs font-semibold text-[var(--color-primary)]">
                                    {{ $answer->selected_answer === 'yes' ? 'Ya' : 'Tidak' }}
                                </span>
                                <p class="mt-2 text-sm text-slate-500">Skor {{ $answer->awarded_score }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </main>
</x-app-layout>
