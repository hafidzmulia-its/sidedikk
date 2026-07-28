<x-app-layout>
    <x-slot name="header">
        <div>
            <div>
                <p class="text-sm font-medium text-slate-500">Panel Admin SIDEDIKK</p>
                <h1 class="mt-1 text-[2.25rem] font-extrabold leading-[1.05] tracking-tight text-slate-950 sm:text-3xl">Screening Selesai</h1>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @include('admin.partials.nav')

        <div class="sid-card p-5">
            <form method="GET" class="grid gap-4 lg:grid-cols-4">
                <label>
                    <span class="sid-label">Kategori risiko</span>
                    <input type="text" name="risk_label" value="{{ $filters['risk_label'] }}" class="sid-input" placeholder="Risiko Rendah">
                </label>
                <label>
                    <span class="sid-label">Email pengguna</span>
                    <input type="text" name="user_email" value="{{ $filters['user_email'] }}" class="sid-input" placeholder="ibu@sid.test">
                </label>
                <label>
                    <span class="sid-label">Dari tanggal</span>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="sid-input">
                </label>
                <label>
                    <span class="sid-label">Sampai tanggal</span>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="sid-input">
                </label>
                <div class="lg:col-span-4">
                    <button type="submit" class="sid-button-primary">Terapkan Filter</button>
                </div>
            </form>
        </div>

        @if ($screenings->isEmpty())
            @include('admin.partials.table-empty', ['title' => 'Belum ada hasil screening', 'message' => 'Data hasil screening selesai akan muncul di sini.'])
        @else
            <div class="sid-card overflow-hidden">
                <div class="sid-table-scroll">
                    <table class="min-w-[900px] text-left text-sm">
                        <thead class="bg-white/80 text-slate-500">
                            <tr>
                                <th class="px-5 py-4 font-semibold">Tanggal</th>
                                <th class="px-5 py-4 font-semibold">Pengguna</th>
                                <th class="px-5 py-4 font-semibold">Skor</th>
                                <th class="px-5 py-4 font-semibold">Risiko</th>
                                <th class="px-5 py-4 font-semibold">Versi</th>
                                <th class="px-5 py-4 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($screenings as $screening)
                                <tr class="bg-white">
                                    <td class="px-5 py-4 text-slate-600">{{ $screening->completed_at?->translatedFormat('d M Y H:i') }}</td>
                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-slate-900">{{ $screening->user?->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $screening->user?->email }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600">{{ $screening->total_score }}/{{ $screening->max_score }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $screening->risk_label_snapshot }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $screening->questionnaire_version_name_snapshot }}</td>
                                    <td class="px-5 py-4">
                                        <a href="{{ route('admin.screenings.show', $screening) }}" class="font-semibold text-[var(--color-primary)]">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pt-1">
                {{ $screenings->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
