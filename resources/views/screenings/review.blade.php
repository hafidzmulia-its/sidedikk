<x-app-layout>
    <x-slot name="header">
        <header class="sid-app-bar border-b border-[#efddef]">
            <a href="{{ route('screenings.questions.show', ['screening' => $screening, 'step' => max(1, $answeredCount)]) }}" class="sid-top-icon" aria-label="Kembali">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <h1 class="flex-1 pr-10 text-center text-[18px] font-semibold text-[var(--color-primary)]">Review Jawaban</h1>
        </header>
    </x-slot>

    <main class="space-y-5 px-5 pb-8 pt-6">
        @if ($errors->has('screening') || $errors->has('submission_key'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-[var(--color-danger)]">
                {{ $errors->first('screening') ?: $errors->first('submission_key') }}
            </div>
        @endif

        <section class="rounded-2xl border border-[#efddef] bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-normal text-[#82737f]">{{ $screening->display_questionnaire_version_name }}</p>
                    <h2 class="mt-2 text-[22px] font-semibold leading-[30px] text-[#221825]">{{ $answeredCount }} dari {{ $totalQuestions }} pertanyaan terisi</h2>
                </div>
                <span class="rounded-full bg-[#f2ddf6] px-4 py-2 text-xs font-semibold text-[#95409e]">Siap Dikirim</span>
            </div>

            <div class="mt-5 space-y-3">
                @foreach ($reviewItems as $index => $item)
                    <article class="rounded-2xl border border-[#efddef] bg-white p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="max-w-[220px]">
                                <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-[#82737f]">Pertanyaan {{ $index + 1 }}</p>
                                <p class="mt-2 text-sm font-medium leading-6 text-[#221825]">{{ $item['question']->text }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-3">
                                <span class="rounded-full px-4 py-2 text-xs font-semibold {{ $item['answer'] === 'yes' ? 'bg-[#f2ddf6] text-[#95409e]' : ($item['answer'] === 'no' ? 'bg-[#f4f4f5] text-[#50434e]' : 'bg-[#fef7e0] text-[#a96900]') }}">
                                    {{ $item['answer'] === 'yes' ? 'Ya' : ($item['answer'] === 'no' ? 'Tidak' : 'Belum dijawab') }}
                                </span>
                                <a href="{{ route('screenings.questions.show', ['screening' => $screening, 'step' => $index + 1]) }}" class="text-xs font-semibold text-[#95409e] hover:underline">Ubah</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <form method="POST" action="{{ route('screenings.submit', $screening) }}" class="mt-6">
                @csrf
                <input type="hidden" name="submission_key" value="{{ $screening->submission_key }}">
                <button type="submit" class="flex h-12 w-full items-center justify-center rounded-full bg-[#95409e] text-sm font-semibold text-white shadow-[0_8px_24px_rgba(149,64,158,0.12)] transition-all hover:opacity-90 active:scale-95" @disabled($answeredCount < $totalQuestions)>
                    Kirim Hasil Screening
                </button>
            </form>
        </section>
    </main>
</x-app-layout>
