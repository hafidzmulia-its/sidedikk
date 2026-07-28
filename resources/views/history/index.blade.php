<x-app-layout>
    <x-slot name="header">
        <header class="sid-app-bar">
            <a href="{{ route('dashboard') }}" class="sid-top-icon" aria-label="Kembali">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <h1 class="text-[18px] font-semibold text-[#95409e]">Riwayat Screening</h1>
            <div class="h-6 w-6"></div>
        </header>
    </x-slot>

    <main class="px-5 pb-8 pt-2">
        <div class="mx-auto flex max-w-2xl flex-col gap-4">
            @if ($screenings->isEmpty())
                <div class="rounded-2xl border border-[#efddef] bg-white p-6 text-center shadow-sm">
                    <p class="text-base font-medium text-[#221825]">Belum ada riwayat screening</p>
                    <p class="mt-2 text-sm font-normal leading-6 text-[#50434e]">Setelah proses screening selesai dan disimpan, hasil akan tampil di halaman ini secara otomatis.</p>
                    <div class="mt-4">
                        <a href="{{ route('dashboard') }}" class="inline-flex h-12 items-center justify-center rounded-full bg-[#95409e] px-6 text-sm font-semibold text-white">Kembali ke Beranda</a>
                    </div>
                </div>
            @else
                @foreach ($screenings as $screening)
                    @php($riskLabel = \Illuminate\Support\Str::lower($screening->risk_label_snapshot ?? ''))

                    <a href="{{ route('history.show', $screening) }}" class="flex items-center justify-between rounded-2xl border border-[#d3c1d0]/30 bg-white p-4 shadow-[0_4px_12px_rgba(149,64,158,0.06)] transition-colors hover:bg-white/90">
                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-medium text-[#50434e]">{{ $screening->completed_at?->translatedFormat('d M Y') }}</span>
                            <span class="text-xs font-normal text-[#82737f]">{{ $screening->gestational_age_weeks_snapshot }} minggu</span>
                            <div class="mt-2 inline-flex items-center rounded-full px-2 py-1 text-[10px] font-bold
                                @if (str_contains($riskLabel, 'rendah')) bg-[#E6F4EA] text-[#137333]
                                @elseif (str_contains($riskLabel, 'sedang')) bg-[#FEF7E0] text-[#B06000]
                                @else bg-[#FDECEF] text-[#C83E50] @endif">
                                {{ $screening->risk_label_snapshot }}
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-[#95409e]">Skor {{ $screening->total_score }}/{{ $screening->max_score }}</span>
                            <span class="material-symbols-outlined text-[#82737f]">chevron_right</span>
                        </div>
                    </a>
                @endforeach

                <div class="mt-4 rounded-xl bg-[#fff7fb] p-4">
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-[20px] text-[#95409e]">event_note</span>
                        <p class="text-xs font-normal leading-5 text-[#50434e]">Lakukan screening secara berkala untuk memantau kesehatan kehamilan Anda.</p>
                    </div>
                </div>

                <div>
                    {{ $screenings->links() }}
                </div>
            @endif
        </div>
    </main>
</x-app-layout>
