<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-slate-500">Panel Admin SIDEDIKK</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-950">Detail Screening Read-Only</h1>
            </div>
            <a href="{{ route('admin.screenings.index') }}" class="sid-button-secondary">Kembali</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @include('admin.partials.nav')

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="sid-card p-6">
                <p class="text-sm font-medium text-slate-500">Ringkasan Hasil</p>
                <div class="mt-5 space-y-3 text-sm text-slate-700">
                    <p><span class="font-semibold">Pengguna:</span> {{ $screening->user?->name }} ({{ $screening->user?->email }})</p>
                    <p><span class="font-semibold">Tanggal:</span> {{ $screening->completed_at?->translatedFormat('d F Y H:i') }}</p>
                    <p><span class="font-semibold">Skor:</span> {{ $screening->total_score }}/{{ $screening->max_score }}</p>
                    <p><span class="font-semibold">Risiko:</span> {{ $screening->risk_label_snapshot }}</p>
                    <p><span class="font-semibold">Usia kehamilan:</span> {{ $screening->gestational_age_weeks_snapshot }} minggu {{ $screening->gestational_age_days_snapshot }} hari</p>
                </div>
            </div>

            <div class="sid-card p-6">
                <p class="text-sm font-medium text-slate-500">Versi Terkunci</p>
                <div class="mt-5 space-y-3 text-sm text-slate-700">
                    <p><span class="font-semibold">Kuesioner:</span> {{ $screening->questionnaire_version_name_snapshot }}</p>
                    <p><span class="font-semibold">Aturan risiko:</span> {{ $screening->risk_rule_version_name_snapshot }}</p>
                    <p><span class="font-semibold">Deskripsi risiko:</span> {{ $screening->risk_description_snapshot }}</p>
                    <p><span class="font-semibold">Rekomendasi:</span> {{ $screening->recommendation_snapshot }}</p>
                </div>
            </div>
        </div>

        <div class="sid-card p-6">
            <p class="text-lg font-bold text-slate-900">Jawaban Tersimpan</p>
            <div class="mt-5 space-y-3">
                @foreach ($screening->answers as $answer)
                    <article class="rounded-[24px] border bg-white p-5">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Pertanyaan {{ $answer->display_order_snapshot }}</p>
                                <p class="mt-2 text-base font-semibold text-slate-900">{{ $answer->question_text_snapshot }}</p>
                            </div>
                            <div class="text-right text-sm text-slate-600">
                                <p class="font-semibold">{{ $answer->selected_answer === 'yes' ? 'Ya' : 'Tidak' }}</p>
                                <p class="mt-1">Skor {{ $answer->awarded_score }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
