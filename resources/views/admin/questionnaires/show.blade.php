<x-app-layout>
    <x-slot name="header">
        <x-admin.page-header
            title="Preview Kuesioner"
            description="Periksa susunan pertanyaan aktif, bantuan singkat, dan skor dalam format tabel yang mudah dipindai."
        >
            <a href="{{ route('admin.questionnaires.index') }}" class="sid-button-secondary">Kembali</a>
            <a href="{{ route('admin.questionnaires.edit', $version) }}" class="sid-button-primary">Edit Pertanyaan</a>
        </x-admin.page-header>
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

        @include('admin.questionnaires.partials.questions-table', [
            'questions' => $version->questions,
            'description' => 'Gunakan tampilan ini untuk memeriksa urutan, isi, bantuan singkat, dan skor setiap butir kuesioner.',
        ])
    </div>
</x-app-layout>
