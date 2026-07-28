<x-app-layout>
    <x-slot name="header">
        <header class="sid-app-bar border-b border-[#efddef]">
            <a href="{{ $step > 1 ? route('screenings.questions.show', ['screening' => $screening, 'step' => $step - 1]) : route('dashboard') }}" class="sid-top-icon" aria-label="Kembali">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <h1 class="flex-1 pr-10 text-center text-[18px] font-semibold text-[var(--color-primary)]">Deteksi Dini</h1>
        </header>
    </x-slot>

    <main class="flex flex-1 flex-col gap-5 px-5 pb-8 pt-5">
        <section class="flex flex-col gap-2">
            <span class="inline-flex w-fit rounded-full bg-[var(--color-primary-soft)] px-3 py-1 text-xs font-semibold text-[var(--color-primary)]">
                Pertanyaan {{ $step }} dari {{ $totalSteps }}
            </span>
            <div>
                <h2 class="text-[18px] font-semibold leading-7 text-[#221825]">Kuesioner Deteksi Dini</h2>
                <p class="mt-1 text-xs font-normal leading-5 text-[#50434e]">Mohon jawab pertanyaan berikut sesuai kondisi Ibu saat ini.</p>
            </div>
        </section>

        <section class="sticky top-0 z-10 -mx-5 border-y border-[#f3e7f5] bg-[#fffdfe] px-5 py-3">
            <div class="flex flex-col gap-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-[var(--color-primary)]">Progres Pengisian</span>
                    <span class="text-xs font-semibold text-[var(--color-primary)]">{{ $progressPercent }}%</span>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-[#efddef]">
                    <div class="h-full rounded-full bg-[#95409e]" style="width: {{ $progressPercent }}%"></div>
                </div>
            </div>
        </section>

        <form method="POST" action="{{ route('screenings.questions.update', ['screening' => $screening, 'step' => $step]) }}" class="flex flex-col gap-4">
            @csrf
            @method('PUT')

            <div class="space-y-3">
                @foreach ($questions as $index => $item)
                    @php
                        $itemAnswer = $answers[$item->id] ?? null;
                        $isCurrent = $item->id === $question->id;
                        $errorMessages = $errors->get('answers.' . $item->id);

                        if ($isCurrent) {
                            $errorMessages = array_merge($errorMessages, $errors->get('answer'));
                        }
                    @endphp

                    <section id="question-{{ $item->id }}" data-question-card class="scroll-mt-32 rounded-[22px] border {{ $isCurrent ? 'border-[#95409e] shadow-[0_10px_24px_rgba(149,64,158,0.08)]' : 'border-[#efddef]' }} bg-white p-3.5">
                        <div class="flex flex-col gap-1.5">
                            <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#95409e]">Pertanyaan {{ $index + 1 }}</span>
                            <h3 class="text-[14px] font-medium leading-5 text-[#221825]">{{ $item->text }}</h3>
                            @if ($item->help_text)
                                <p class="text-[11px] font-normal leading-5 text-[#50434e]">{{ $item->help_text }}</p>
                            @endif
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <label class="relative flex min-h-[48px] cursor-pointer items-center rounded-[18px] border border-[#d3c1d0] bg-white p-3 transition-all hover:bg-[#fff7fb]">
                                <input type="radio" name="answers[{{ $item->id }}]" value="yes" class="peer sr-only" @checked($itemAnswer === 'yes')>
                                <div class="mr-3 h-5 w-5 flex-shrink-0 rounded-full border-2 border-[#82737f] transition-all peer-checked:border-[5px] peer-checked:border-[#95409e]"></div>
                                <span class="text-sm font-normal text-[#221825] peer-checked:font-semibold">Ya</span>
                            </label>

                            <label class="relative flex min-h-[48px] cursor-pointer items-center rounded-[18px] border {{ $itemAnswer === 'no' ? 'border-[#95409e] shadow-[0_4px_12px_rgba(149,64,158,0.06)]' : 'border-[#d3c1d0]' }} bg-white p-3 transition-all hover:bg-[#fff7fb]">
                                <input type="radio" name="answers[{{ $item->id }}]" value="no" class="peer sr-only" @checked($itemAnswer === 'no')>
                                <div class="mr-3 h-5 w-5 flex-shrink-0 rounded-full border-2 border-[#82737f] transition-all peer-checked:border-[5px] peer-checked:border-[#95409e]"></div>
                                <span class="text-sm font-normal text-[#221825] peer-checked:font-semibold">Tidak</span>
                            </label>
                        </div>

                        @if (! empty($errorMessages))
                            <x-input-error :messages="$errorMessages" class="mt-3" />
                        @endif
                    </section>
                @endforeach
            </div>

            <div id="screening-submit-area" class="pt-1">
                <button type="submit" class="mx-auto flex h-10 w-full max-w-[220px] items-center justify-center rounded-full bg-[#95409e] px-5 text-[13px] font-semibold text-white shadow-[0_8px_24px_rgba(149,64,158,0.12)] transition-all hover:opacity-90 active:scale-95">
                    {{ $answeredCount === $totalSteps ? 'Lanjut ke Review' : 'Simpan Jawaban' }}
                </button>
            </div>
        </form>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const activeQuestion = document.getElementById('question-{{ $question->id }}');
            const questionCards = Array.from(document.querySelectorAll('[data-question-card]'));
            const submitArea = document.getElementById('screening-submit-area');

            if (activeQuestion) {
                window.requestAnimationFrame(function () {
                    activeQuestion.scrollIntoView({
                        block: 'start',
                        behavior: 'smooth',
                    });
                });
            }

            document.querySelectorAll('input[type="radio"][name^="answers["]').forEach(function (input) {
                input.addEventListener('change', function () {
                    const currentCard = input.closest('[data-question-card]');
                    const currentIndex = questionCards.indexOf(currentCard);
                    const nextCard = questionCards[currentIndex + 1];

                    window.setTimeout(function () {
                        if (nextCard) {
                            nextCard.scrollIntoView({
                                block: 'start',
                                behavior: 'smooth',
                            });

                            return;
                        }

                        submitArea?.scrollIntoView({
                            block: 'nearest',
                            behavior: 'smooth',
                        });
                    }, 180);
                });
            });
        });
    </script>
</x-app-layout>
