<x-app-layout>
    <x-slot name="header">
        <x-admin.page-header
            :title="$pageTitle"
            description="Kelola teks, bantuan singkat, skor, dan status aktif pertanyaan dari satu halaman kerja admin."
        >
            <a href="{{ route('admin.questionnaires.index') }}" class="sid-button-secondary">Kembali</a>
        </x-admin.page-header>
    </x-slot>

    @php
        $questionRows = old('questions', $questions);
    @endphp

    <div class="space-y-6">
        @include('admin.partials.nav')

        <form
            method="POST"
            action="{{ $formAction }}"
            x-data='{
                questions: @json(array_values($questionRows)),
                addQuestion() {
                    this.questions.push({ text: "", help_text: "", score_yes: 0, score_no: 0, is_active: true });
                },
                removeQuestion(index) {
                    if (this.questions.length > 1) {
                        this.questions.splice(index, 1);
                    }
                },
                totalQuestions() {
                    return this.questions.length;
                },
                activeQuestions() {
                    return this.questions.filter((question) => Boolean(question.is_active)).length;
                },
                maxScore() {
                    return this.questions.reduce((total, question) => total + (Boolean(question.is_active) ? Number(question.score_yes || 0) : 0), 0);
                }
            }'
            class="space-y-6"
        >
            @csrf
            @if ($formMethod !== 'POST')
                @method($formMethod)
            @endif

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(20rem,0.42fr)]">
                <div class="sid-card p-6">
                    <label class="block">
                        <span class="sid-label">Judul kuesioner</span>
                        <input type="text" name="title" value="{{ old('title', $version->title) }}" class="sid-input mt-2" required>
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </label>

                    <div class="mt-5 rounded-[24px] border border-[#f0e2f3] bg-[#fffafd] px-4 py-4">
                        <p class="text-sm font-semibold text-slate-900">Catatan pengelolaan</p>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Satu set pertanyaan ini langsung dipakai sebagai kuesioner aktif. Perubahan skor, status aktif, dan isi pertanyaan dapat disimpan dari halaman ini.</p>
                    </div>
                </div>

                <div class="sid-card p-6">
                    <p class="text-sm font-semibold text-slate-900">Ringkasan cepat</p>
                    <div class="mt-4 grid gap-3 sm:grid-cols-3 xl:grid-cols-1">
                        <div class="rounded-[20px] border border-[#f0e2f3] bg-[#fffafd] px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total pertanyaan</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-950" x-text="totalQuestions()"></p>
                        </div>
                        <div class="rounded-[20px] border border-[#f0e2f3] bg-[#fffafd] px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Pertanyaan aktif</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-950" x-text="activeQuestions()"></p>
                        </div>
                        <div class="rounded-[20px] border border-[#f0e2f3] bg-[#fffafd] px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Skor maksimal</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-950" x-text="maxScore()"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sid-card p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-lg font-semibold text-slate-900">Pertanyaan Screening</p>
                        <p class="mt-1 text-sm text-slate-500">Tambah, hapus, dan sesuaikan skor tiap pertanyaan dari daftar berikut.</p>
                    </div>
                    <button type="button" class="sid-button-secondary" @click="addQuestion()">Tambah Pertanyaan</button>
                </div>

                <x-input-error :messages="$errors->get('questions')" class="mt-4" />

                <div class="mt-5 space-y-4">
                    <template x-for="(question, index) in questions" :key="index">
                        <section class="rounded-[24px] border border-[#eddcf0] bg-white p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <span class="inline-flex rounded-full bg-[var(--color-primary-soft)] px-3 py-1 text-xs font-semibold text-[var(--color-primary)]" x-text="`Pertanyaan ${index + 1}`"></span>
                                    <p class="mt-3 text-sm leading-6 text-slate-500">Pertanyaan akan ditampilkan sesuai urutan kartu ini pada alur screening pengguna.</p>
                                </div>
                                <button
                                    type="button"
                                    class="rounded-full border border-rose-200 px-3 py-2 text-xs font-semibold text-[var(--color-danger)] transition hover:bg-rose-50"
                                    @click="removeQuestion(index)"
                                >
                                    Hapus
                                </button>
                            </div>

                            <div class="mt-5 grid gap-4">
                                <label class="block">
                                    <span class="sid-label">Teks pertanyaan</span>
                                    <textarea x-model="question.text" :name="`questions[${index}][text]`" class="sid-input mt-2 min-h-24" required></textarea>
                                </label>

                                <label class="block">
                                    <span class="sid-label">Bantuan singkat</span>
                                    <textarea x-model="question.help_text" :name="`questions[${index}][help_text]`" class="sid-input mt-2 min-h-20"></textarea>
                                </label>

                                <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_minmax(12rem,0.8fr)]">
                                    <label class="block">
                                        <span class="sid-label">Skor jika Ya</span>
                                        <input x-model="question.score_yes" :name="`questions[${index}][score_yes]`" type="number" min="0" max="20" class="sid-input mt-2" required>
                                    </label>
                                    <label class="block">
                                        <span class="sid-label">Skor jika Tidak</span>
                                        <input x-model="question.score_no" :name="`questions[${index}][score_no]`" type="number" min="0" max="20" class="sid-input mt-2" required>
                                    </label>
                                    <label class="flex items-center gap-3 rounded-[20px] border border-[#efe5f3] bg-[#fffafd] px-4 py-3 md:mt-7">
                                        <input type="checkbox" value="1" :name="`questions[${index}][is_active]`" x-model="question.is_active" class="h-5 w-5 text-[var(--color-primary)]">
                                        <span class="text-sm font-semibold text-slate-700">Pertanyaan aktif</span>
                                    </label>
                                </div>
                            </div>
                        </section>
                    </template>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="sid-button-primary">Simpan Perubahan</button>
                <button type="button" class="sid-button-secondary" @click="addQuestion()">Tambah Pertanyaan Lagi</button>
                @if ($version->exists)
                    <a href="{{ route('admin.questionnaires.show', $version) }}" class="sid-button-secondary">Preview</a>
                @endif
            </div>
        </form>
    </div>
</x-app-layout>
