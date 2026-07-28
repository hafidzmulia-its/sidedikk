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
        <section class="overflow-hidden rounded-[28px] border border-[#efddef] bg-white shadow-[0_12px_36px_rgba(149,64,158,0.08)]">
            <div class="bg-[linear-gradient(180deg,#fff8fc_0%,#ffffff_100%)] px-5 pb-5 pt-6">
                <div class="flex items-start gap-4">
                    <div class="relative flex h-16 w-16 shrink-0 items-center justify-center rounded-[22px] bg-[linear-gradient(180deg,#fff2fa_0%,#f5e5f8_100%)] shadow-[0_10px_22px_rgba(149,64,158,0.08)]">
                        <img src="{{ asset('icon.png') }}" alt="SIDEDIKK" class="h-11 w-11 rounded-[16px] object-contain">
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#82737f]">Ringkasan Hasil</p>
                        <div class="mt-2">
                            <x-risk-badge :label="$screening->risk_label_snapshot" size="lg" />
                        </div>
                    </div>
                </div>

                <div class="mt-5 rounded-[24px] border border-[#f0e2f3] bg-white px-4 py-4">
                    <div class="flex items-end justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#82737f]">Skor Ibu</p>
                            <h2 class="mt-2 text-[40px] font-bold leading-none text-[#221825]">
                                {{ $screening->total_score }}
                                <span class="text-[30px] font-semibold text-[#82737f]">/ {{ $screening->max_score }}</span>
                            </h2>
                        </div>
                        <span class="material-symbols-outlined text-[34px] text-[#d7bfdc]">monitor_heart</span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-[#50434e]">
                        {{ $screening->display_risk_description }}
                    </p>
                </div>

                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] border border-[#f0e2f3] bg-white px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#82737f]">Waktu Screening</p>
                        <p class="mt-2 text-sm font-semibold leading-6 text-[#221825]">
                            {{ $screening->completed_at?->translatedFormat('d F Y, H:i') }}
                        </p>
                    </div>
                    <div class="rounded-[22px] border border-[#f0e2f3] bg-white px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#82737f]">Usia Kehamilan</p>
                        <p class="mt-2 text-sm font-semibold leading-6 text-[#221825]">
                            {{ $screening->gestational_age_weeks_snapshot }} minggu {{ $screening->gestational_age_days_snapshot }} hari
                        </p>
                    </div>
                </div>

                <div class="mt-4 rounded-[22px] border border-[#f0e2f3] bg-white p-4">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined mt-0.5 text-[#d3a100]">lightbulb</span>
                        <div>
                            <p class="text-sm font-semibold text-[#221825]">Saran untuk Ibu</p>
                            <p class="mt-2 text-sm leading-6 text-[#50434e]">
                                {{ $screening->display_recommendation }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="sid-card p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-[#221825]">Jawaban Screening</h2>
                    <p class="mt-1 text-sm text-[#82737f]">Ringkasan jawaban yang Ibu pilih saat mengisi screening.</p>
                </div>
                <div class="rounded-full bg-[#fff7fb] px-3 py-1 text-xs font-semibold text-[#95409e]">
                    {{ $screening->answers->count() }} pertanyaan
                </div>
            </div>

            <div class="mt-4 space-y-3">
                @foreach ($screening->answers as $answer)
                    <article class="rounded-[24px] border border-[#f0e2f3] bg-white p-4">
                        <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-start">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#b09eab]">Pertanyaan {{ $answer->display_order_snapshot }}</p>
                                <p class="mt-2 text-sm font-medium leading-6 text-[#221825]">{{ $answer->question_text_snapshot }}</p>
                            </div>
                            <div class="sm:justify-self-end">
                                <span class="inline-flex w-full items-center justify-center rounded-full px-4 py-2 text-sm font-semibold sm:min-w-[112px] sm:w-auto
                                    {{ $answer->selected_answer === 'yes'
                                        ? 'bg-[#fdecef] text-[#c83e50]'
                                        : 'bg-[#f1ecf8] text-[#95409e]' }}">
                                    {{ $answer->selected_answer === 'yes' ? 'Ya' : 'Tidak' }}
                                </span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </main>
</x-app-layout>
