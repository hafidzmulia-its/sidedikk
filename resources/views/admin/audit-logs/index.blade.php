<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Panel Admin SIDEDIKK</p>
            <h1 class="mt-1 text-[2.25rem] font-extrabold leading-[1.05] tracking-tight text-slate-950 sm:text-3xl">Audit Log</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        @include('admin.partials.nav')

        <div class="sid-card p-5">
            <form method="GET" class="flex flex-wrap items-end gap-4">
                <label class="min-w-[280px]">
                    <span class="sid-label">Cari aksi</span>
                    <input type="text" name="search" value="{{ $search }}" class="sid-input" placeholder="admin.screenings.exported">
                </label>
                <button type="submit" class="sid-button-primary">Filter</button>
            </form>
        </div>

        @if ($logs->isEmpty())
            @include('admin.partials.table-empty', ['title' => 'Belum ada audit log', 'message' => 'Aktivitas administrasi yang dicatat akan muncul di sini.'])
        @else
            <div class="sid-card overflow-hidden">
                <div class="sid-table-scroll">
                    <table class="min-w-[980px] text-left text-sm">
                        <thead class="bg-white/80 text-slate-500">
                            <tr>
                                <th class="px-5 py-4 font-semibold">Waktu</th>
                                <th class="px-5 py-4 font-semibold">Aktor</th>
                                <th class="px-5 py-4 font-semibold">Aksi</th>
                                <th class="px-5 py-4 font-semibold">Subjek</th>
                                <th class="px-5 py-4 font-semibold">Metadata</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($logs as $log)
                                <tr class="bg-white align-top">
                                    <td class="px-5 py-4 text-slate-600">{{ $log->created_at?->translatedFormat('d M Y H:i') }}</td>
                                    <td class="px-5 py-4 text-slate-700">{{ $log->actor?->email ?? 'Sistem' }}</td>
                                    <td class="px-5 py-4 text-slate-700">{{ $log->action }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ class_basename($log->subject_type ?? '-') }} #{{ $log->subject_id ?? '-' }}</td>
                                    <td class="px-5 py-4 text-xs text-slate-500">{{ json_encode($log->safe_metadata, JSON_UNESCAPED_UNICODE) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pt-1">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
