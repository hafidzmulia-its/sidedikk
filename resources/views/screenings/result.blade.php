@php($riskLabel = \Illuminate\Support\Str::lower($screening->risk_label_snapshot ?? ''))

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

        <section class="rounded-2xl border border-[#efddef] bg-white p-6 text-center shadow-sm">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-[#fff7fb] text-[#95409e] shadow-sm">
                <span class="material-symbols-outlined text-5xl">sentiment_satisfied</span>
            </div>

            <p class="mt-5 text-sm font-normal text-[#82737f]">Skor Ibu</p>
            <h2 class="mt-2 text-[44px] font-bold leading-none text-[#221825]">
                {{ $screening->total_score }}
                <span class="text-2xl font-semibold text-[#82737f]">/ {{ $screening->max_score }}</span>
            </h2>

            <div class="mt-4 text-[22px] font-semibold leading-[30px]
                @if (str_contains($riskLabel, 'rendah')) text-[#238a57]
                @elseif (str_contains($riskLabel, 'sedang')) text-[#a96900]
                @else text-[#c83e50] @endif">
                {{ $screening->risk_label_snapshot }}
            </div>

            <p class="mx-auto mt-4 max-w-[260px] text-sm font-normal leading-6 text-[#50434e]">
                {{ $screening->display_risk_description }}
            </p>

            <div class="mt-6 rounded-2xl bg-[#fff7fb] p-4 text-left">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined mt-0.5 text-[#d3a100]">lightbulb</span>
                    <div>
                        <p class="text-sm font-medium text-[#221825]">Saran</p>
                        <p class="mt-1 text-sm font-normal leading-6 text-[#50434e]">
                            {{ $screening->display_recommendation }}
                        </p>
                    </div>
                </div>
            </div>

            <x-medical-disclaimer class="mt-4" />

            <div class="mt-6 space-y-3">
                <a href="{{ route('dashboard') }}" class="flex h-12 w-full items-center justify-center rounded-full bg-[#95409e] text-sm font-semibold text-white shadow-[0_8px_24px_rgba(149,64,158,0.12)] transition-all hover:opacity-90 active:scale-95">
                    Selesai
                </a>
                <a href="{{ route('history.show', $screening) }}" class="flex h-12 w-full items-center justify-center rounded-full border border-[#d3c1d0] bg-white text-sm font-semibold text-[#95409e] transition-all hover:bg-[#fff7fb]">
                    Lihat Detail Jawaban
                </a>
            </div>
        </section>
    </main>
</x-app-layout>
