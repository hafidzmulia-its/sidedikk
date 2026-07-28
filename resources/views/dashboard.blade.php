<x-app-layout>
    <x-slot name="header">
        <header class="px-5 pb-4 pt-5">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h1 class="text-[22px] font-semibold leading-7 text-[var(--color-primary)]">Halo, Ibu {{ $user->name }}</h1>
                    <p class="mt-1 text-sm font-normal text-[#82737f]">Semoga hari Ibu menyenangkan.</p>
                </div>
            </div>
        </header>
    </x-slot>

    @php($estimatedDueDate = $user->hpht_date?->copy()->addDays(280))

    <div class="space-y-6 pb-8 px-4">
        @if ($errors->has('screening'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-[var(--color-danger)]">
                {{ $errors->first('screening') }}
            </div>
        @endif

        <section class="relative overflow-hidden rounded-2xl border border-[#d3c1d0]/30 bg-white p-4 shadow-[0_8px_24px_rgba(0,0,0,0.04)]">
            <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-[#fff7fb] opacity-60 blur-3xl"></div>
            <h2 class="mb-3 text-xs font-medium uppercase tracking-[0.08em] text-[#82737f]">Usia Kehamilan</h2>

            <div class="relative z-10 mb-4 flex items-center justify-between gap-4">
                <div>
                    <p class="text-[20px] font-bold leading-[28px] text-[var(--color-primary)]">
                        {{ $pregnancyAge['weeks'] }} minggu {{ $pregnancyAge['days'] }} hari
                    </p>
                    <p class="mt-1 text-xs font-normal text-[#82737f]">Pantau perkembangan kehamilan Ibu secara berkala.</p>
                </div>
                <div class="flex  w-24 items-center justify-center rounded-full border-4 border-white bg-white shadow-sm">
                    <img src="{{ asset('assets/baby.png') }}" alt="Ilustrasi SIDEDIKK" class="h-full w-full rounded-full object-cover opacity-90">
                </div>
            </div>

            <div class="mb-4 h-2.5 w-full overflow-hidden rounded-full bg-[#efddef]">
                <div class="h-2.5 rounded-full bg-[#95409E]" style="width: {{ min(100, max(8, round(($pregnancyAge['days_total'] / 280) * 100))) }}%"></div>
            </div>

            <div class="flex items-center justify-between border-t border-[#fbe8fb] pt-4 text-xs font-normal text-[#82737f]">
                <span class="rounded-lg bg-white px-3 py-1.5 font-medium text-[var(--color-primary)]">Trimester {{ $pregnancyAge['trimester'] }}</span>
                <span>Perkiraan lahir {{ $estimatedDueDate?->translatedFormat('d M Y') ?? '-' }}</span>
            </div>
        </section>

        <section>
            <h3 class="mb-4 text-[16px] font-semibold leading-6 text-[#221825]">Layanan Utama</h3>
            <div class="grid grid-cols-1 gap-4">
                <div class="rounded-2xl border border-[#d3c1d0]/30 bg-white p-4 shadow-[0_8px_24px_rgba(0,0,0,0.04)]">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#95409E] text-white shadow-sm">
                                <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;">favorite</span>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-[#221825]">Deteksi Dini</h4>
                                <p class="mt-1 max-w-[180px] text-xs font-normal leading-4 text-[#82737f]">Isi kuesioner untuk membantu skrining awal risiko kehamilan.</p>
                            </div>
                        </div>

                        @if ($activeScreening)
                            <a href="{{ $activeScreeningNextStep ? route('screenings.questions.show', ['screening' => $activeScreening, 'step' => $activeScreeningNextStep]) : route('screenings.review', $activeScreening) }}" class="text-[#82737f] transition-colors hover:text-[var(--color-primary)]">
                                <span class="material-symbols-outlined">chevron_right</span>
                            </a>
                        @else
                            <form method="POST" action="{{ route('screenings.start') }}">
                                @csrf
                                <button type="submit" class="text-[#82737f] transition-colors hover:text-[var(--color-primary)]">
                                    <span class="material-symbols-outlined">chevron_right</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <a href="{{ route('education.index') }}" class="rounded-2xl border border-[#d3c1d0]/30 bg-white p-4 shadow-[0_8px_24px_rgba(0,0,0,0.04)] transition-all">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#544659] text-white shadow-sm">
                                <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;">menu_book</span>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-[#221825]">Edukasi Kehamilan</h4>
                                <p class="mt-1 max-w-[180px] text-xs font-normal leading-4 text-[#82737f]">Baca panduan singkat yang relevan untuk Ibu hamil.</p>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-[#82737f]">chevron_right</span>
                    </div>
                </a>
            </div>
        </section>

        <section class="pb-4">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-[16px] font-semibold leading-6 text-[#221825]">Riwayat Screening</h3>
                <a href="{{ route('history.index') }}" class="px-2 py-1 text-xs font-medium text-[var(--color-primary)] hover:underline">Lihat semua</a>
            </div>

            @if ($latestScreening)
                @php($riskLabel = \Illuminate\Support\Str::lower($latestScreening->risk_label_snapshot ?? ''))
                <a href="{{ route('history.show', $latestScreening) }}" class="flex items-center justify-between rounded-2xl border border-[#d3c1d0]/30 bg-white p-4 shadow-[0_8px_24px_rgba(0,0,0,0.04)]">
                    <div>
                        <p class="mb-2 text-xs font-normal text-[#82737f]">Screening terakhir</p>
                        <p class="text-sm font-semibold text-[#221825]">
                            {{ $latestScreening->completed_at?->translatedFormat('d M Y') }} • {{ $latestScreening->gestational_age_weeks_snapshot }} minggu
                        </p>
                    </div>
                    <div class="rounded-full border px-4 py-2 text-xs font-bold
                        @if (str_contains($riskLabel, 'rendah')) border-[#c8e6c9] bg-[#e8f5e9] text-[#2e7d32]
                        @elseif (str_contains($riskLabel, 'sedang')) border-[#f3d9a6] bg-[#fef7e0] text-[#b06000]
                        @else border-[#f5c6cb] bg-[#fdecef] text-[#c83e50] @endif">
                        {{ $latestScreening->risk_label_snapshot }}
                    </div>
                </a>
            @else
                <div class="rounded-2xl border border-dashed border-[#d3c1d0] bg-white p-6 text-sm font-normal text-[#50434e] shadow-[0_8px_24px_rgba(0,0,0,0.04)]">
                    Belum ada hasil screening yang tersimpan.
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
