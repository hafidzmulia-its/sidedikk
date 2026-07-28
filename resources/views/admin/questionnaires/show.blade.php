<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Panel Admin SIDEDIKK</p>
                <h1 class="mt-1 text-[2rem] font-semibold leading-tight text-slate-950">Preview Kuesioner</h1>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.questionnaires.index') }}" class="sid-button-secondary">Kembali</a>
                <a href="{{ route('admin.questionnaires.edit', $version) }}" class="sid-button-primary">Edit Pertanyaan</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @include('admin.partials.nav')

        @if (session('status'))
            <div class="rounded-[24px] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-[var(--color-success)]">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-4 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)]">
            <div class="sid-card p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[var(--color-primary)]">Kuesioner Aktif</p>
                <h2 class="mt-2 text-2xl font-semibold leading-tight text-slate-950">{{ $version->title }}</h2>
                <p class="mt-3 text-sm leading-6 text-slate-500">Preview ini menampilkan susunan pertanyaan, bantuan singkat, dan skor jawaban untuk memastikan alur screening siap digunakan.</p>
            </div>

            <div class="sid-card p-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-slate-500">Status</span>
                        <x-admin.partials.status-badge :status="$version->status" />
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-slate-500">Jumlah pertanyaan</span>
                        <span class="text-sm font-semibold text-slate-900">{{ $summary['total_questions'] }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-slate-500">Pertanyaan aktif</span>
                        <span class="text-sm font-semibold text-slate-900">{{ $summary['active_questions'] }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-slate-500">Skor maksimal</span>
                        <span class="text-sm font-semibold text-slate-900">{{ $summary['max_score'] }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-slate-500">Terakhir diperbarui</span>
                        <span class="text-sm font-semibold text-slate-900">{{ $version->updated_at?->translatedFormat('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="sid-card p-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-lg font-semibold text-slate-900">Daftar Pertanyaan</p>
                    <p class="mt-1 text-sm text-slate-500">Gunakan tampilan ini untuk memeriksa urutan, isi, dan skor setiap butir kuesioner.</p>
                </div>
                <span class="rounded-full bg-[var(--color-primary-soft)] px-3 py-1 text-xs font-semibold text-[var(--color-primary)]">{{ $summary['total_questions'] }} butir</span>
            </div>

            <div class="mt-5 space-y-4">
                @foreach ($version->questions as $question)
                    <article class="rounded-[24px] border border-[#f0e2f3] bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <span class="inline-flex rounded-full bg-[var(--color-primary-soft)] px-3 py-1 text-xs font-semibold text-[var(--color-primary)]">Pertanyaan {{ $question->display_order }}</span>
                                <h3 class="mt-3 text-base font-semibold leading-6 text-slate-950">{{ $question->text }}</h3>
                                @if ($question->help_text)
                                    <p class="mt-2 text-sm leading-6 text-slate-500">{{ $question->help_text }}</p>
                                @endif
                            </div>
                            <span class="flex-shrink-0 rounded-full px-3 py-1 text-xs font-semibold {{ $question->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $question->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-[20px] border border-[#efe5f3] bg-[#fffafd] px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Jawaban Ya</p>
                                <p class="mt-2 text-xl font-semibold text-slate-950">{{ $question->score_yes }}</p>
                            </div>
                            <div class="rounded-[20px] border border-[#efe5f3] bg-[#fffafd] px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Jawaban Tidak</p>
                                <p class="mt-2 text-xl font-semibold text-slate-950">{{ $question->score_no }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
