<div class="sid-card overflow-hidden">
    <div class="flex items-center justify-between gap-4 px-6 pt-6">
        <div>
            <p class="text-lg font-semibold text-slate-900">Daftar Pertanyaan</p>
            <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
        </div>
        <span class="rounded-full bg-[var(--color-primary-soft)] px-3 py-1 text-xs font-semibold text-[var(--color-primary)]">
            {{ $questions->count() }} butir
        </span>
    </div>

    <div class="mt-5 sid-table-scroll">
        <table class="min-w-[860px] text-left text-sm">
            <thead class="bg-white/80 text-slate-500">
                <tr>
                    <th class="px-5 py-4 font-semibold">No</th>
                    <th class="px-5 py-4 font-semibold">Pertanyaan</th>
                    <th class="px-5 py-4 font-semibold">Bantuan</th>
                    <th class="px-5 py-4 font-semibold">Skor Ya</th>
                    <th class="px-5 py-4 font-semibold">Skor Tidak</th>
                    <th class="px-5 py-4 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach ($questions as $question)
                    <tr class="bg-white align-top">
                        <td class="px-5 py-4 font-semibold text-slate-900">{{ $question->display_order }}</td>
                        <td class="px-5 py-4">
                            <p class="font-medium text-slate-900">{{ $question->text }}</p>
                        </td>
                        <td class="px-5 py-4 text-slate-600">
                            {{ $question->help_text ?: '-' }}
                        </td>
                        <td class="px-5 py-4 text-slate-600">{{ $question->score_yes }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ $question->score_no }}</td>
                        <td class="px-5 py-4">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $question->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $question->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
