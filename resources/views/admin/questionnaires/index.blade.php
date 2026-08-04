<x-app-layout>
    <x-slot name="header">
        <x-admin.page-header
            title="Kelola Kuesioner"
            description="Pantau ringkasan kuesioner aktif dan kelola daftar pertanyaan dengan pola tabel horizontal yang seragam."
        >
            @if ($questionnaire)
                <a href="{{ route('admin.questionnaires.edit', $questionnaire) }}" class="sid-button-primary">Ubah Pertanyaan</a>
            @else
                <a href="{{ route('admin.questionnaires.create') }}" class="sid-button-primary">Buat Kuesioner</a>
            @endif
        </x-admin.page-header>
    </x-slot>

    <div class="space-y-6">
        @include('admin.partials.nav')

        @if (session('status'))
            <div class="rounded-[24px] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-[var(--color-success)]">
                {{ session('status') }}
            </div>
        @endif

        @if (! $questionnaire || ! $summary)
            <div class="sid-card px-5 py-10 text-center">
                <p class="text-lg font-semibold text-slate-900">Kuesioner belum disiapkan</p>
                <p class="mt-2 text-sm leading-6 text-slate-500">Buat satu set pertanyaan aktif agar alur screening bisa langsung digunakan dari panel admin.</p>
                <a href="{{ route('admin.questionnaires.create') }}" class="sid-button-primary mx-auto mt-5 inline-flex">Siapkan Sekarang</a>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="sid-card p-5">
                    <p class="text-sm font-medium text-slate-500">Jumlah Pertanyaan</p>
                    <p class="mt-3 text-3xl font-bold text-slate-950">{{ $summary['total_questions'] }}</p>
                </div>
                <div class="sid-card p-5">
                    <p class="text-sm font-medium text-slate-500">Pertanyaan Aktif</p>
                    <p class="mt-3 text-3xl font-bold text-slate-950">{{ $summary['active_questions'] }}</p>
                </div>
                <div class="sid-card p-5">
                    <p class="text-sm font-medium text-slate-500">Skor Maksimal</p>
                    <p class="mt-3 text-3xl font-bold text-slate-950">{{ $summary['max_score'] }}</p>
                </div>
                <div class="sid-card p-5">
                    <p class="text-sm font-medium text-slate-500">Status</p>
                    <div class="mt-3">
                        <x-admin.partials.status-badge :status="$questionnaire->status" />
                    </div>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]">
                <div class="sid-card p-6">
                    <div class="flex flex-col gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[var(--color-primary)]">Kuesioner Aktif</p>
                            <h2 class="mt-2 text-2xl font-semibold leading-tight text-slate-950">{{ $questionnaire->title }}</h2>
                            <p class="mt-3 text-sm leading-6 text-slate-500">Admin dapat menambah, menghapus, menonaktifkan, dan mengatur skor setiap pertanyaan langsung dari satu halaman pengelolaan.</p>
                        </div>

                        <dl class="space-y-3 rounded-[24px] border border-[#f0e2f3] bg-[#fffafd] p-4">
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-sm text-slate-500">Versi internal</dt>
                                <dd class="text-sm font-semibold text-slate-900">{{ $questionnaire->version_number }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-sm text-slate-500">Terakhir diperbarui</dt>
                                <dd class="text-sm font-semibold text-slate-900">{{ $questionnaire->updated_at?->translatedFormat('d M Y H:i') }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-sm text-slate-500">Dipublikasikan</dt>
                                <dd class="text-sm font-semibold text-slate-900">{{ $questionnaire->published_at?->translatedFormat('d M Y H:i') ?? '-' }}</dd>
                            </div>
                        </dl>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('admin.questionnaires.edit', $questionnaire) }}" class="sid-button-primary">Edit Pertanyaan</a>
                            <a href="{{ route('admin.questionnaires.show', $questionnaire) }}" class="sid-button-secondary">Preview Lengkap</a>
                        </div>
                    </div>
                </div>

                @include('admin.questionnaires.partials.questions-table', [
                    'questions' => $questionnaire->questions,
                    'description' => 'Tampilan cepat untuk meninjau teks pertanyaan dan pengaturan skornya.',
                ])
            </div>
        @endif
    </div>
</x-app-layout>
