<x-app-layout>
    <x-slot name="header">
        <header class="sid-app-bar border-b border-[#efddef]">
            <a href="{{ route('dashboard') }}" class="sid-top-icon" aria-label="Kembali">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <h1 class="flex-1 text-center text-[18px] font-semibold text-[#95409e]">Hasil Screening</h1>
            <div class="h-10 w-10"></div>
        </header>
    </x-slot>

    <main class="space-y-5 px-5 pb-8 pt-6">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-[var(--color-success)]">
                {{ session('status') }}
            </div>
        @endif

        <section class="overflow-hidden rounded-[28px] border border-[#efddef] bg-white shadow-[0_12px_36px_rgba(149,64,158,0.08)]">
            <div class="bg-[linear-gradient(180deg,#fff7fb_0%,#ffffff_100%)] px-6 pb-6 pt-7 text-center">
                <div class="relative mx-auto h-24 w-24">
                    <div class="absolute inset-0 rounded-full bg-[radial-gradient(circle_at_top,#fde9f7_0%,#f4d8f3_52%,#edd2ef_100%)] shadow-[0_14px_28px_rgba(149,64,158,0.14)]"></div>
                    <div class="absolute inset-[10px] overflow-hidden rounded-full border border-white/80 bg-white shadow-[0_10px_22px_rgba(149,64,158,0.10)]">
                        <img src="{{ asset('icon.png') }}" alt="SIDEDIKK" class="h-full w-full object-contain p-2">
                    </div>
                    <div class="absolute -bottom-1 -right-1 flex h-8 w-8 items-center justify-center rounded-full border-4 border-white bg-[#95409e] text-white shadow-[0_8px_18px_rgba(149,64,158,0.16)]">
                        <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">favorite</span>
                    </div>
                </div>

                <p class="mt-5 text-xs font-semibold uppercase tracking-[0.18em] text-[#82737f]">Skor Kumulatif</p>
                <h2 class="mt-3 text-[44px] font-bold leading-none text-[#221825]">
                    {{ $screening->total_score }}
                    <span class="text-2xl font-semibold text-[#82737f]">/ {{ $screening->max_score }}</span>
                </h2>

                <x-risk-badge :label="$screening->risk_label_snapshot" size="lg" class="mt-4" />

                <p class="mx-auto mt-4 max-w-[280px] text-sm leading-6 text-[#50434e]">
                    {{ $screening->display_risk_description }}
                </p>
            </div>

            <div class="space-y-4 px-6 pb-6">
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] border border-[#f0e2f3] bg-[#fffafd] px-4 py-4 text-left">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#82737f]">Tanggal Screening</p>
                        <p class="mt-2 text-sm font-semibold text-[#221825]">{{ $screening->completed_at?->translatedFormat('d F Y, H:i') }}</p>
                    </div>
                    <div class="rounded-[22px] border border-[#f0e2f3] bg-[#fffafd] px-4 py-4 text-left">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#82737f]">Usia Kehamilan</p>
                        <p class="mt-2 text-sm font-semibold text-[#221825]">{{ $screening->gestational_age_weeks_snapshot }} minggu {{ $screening->gestational_age_days_snapshot }} hari</p>
                    </div>
                </div>

                <div class="rounded-[22px] border border-[#f0e2f3] bg-white p-4">
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

                <div class="space-y-3 pt-1">
                    <a href="{{ route('history.show', $screening) }}" class="flex h-12 w-full items-center justify-center rounded-full bg-[#95409e] text-sm font-semibold text-white shadow-[0_8px_24px_rgba(149,64,158,0.12)] transition-all hover:opacity-90 active:scale-95">
                        Review Detail Hasil
                    </a>
                    <a href="{{ route('dashboard') }}" class="flex h-12 w-full items-center justify-center rounded-full border border-[#d3c1d0] bg-white text-sm font-semibold text-[#95409e] transition-all hover:bg-[#fff7fb]">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </section>
    </main>
</x-app-layout>
