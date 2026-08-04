<x-app-layout>
    <x-slot name="header">
        <x-admin.page-header
            title="Versi Aturan Risiko"
            description="Kelola aturan klasifikasi risiko dengan pola tampilan dan aksi yang konsisten di seluruh panel admin."
        >
            <a href="{{ route('admin.risk-rules.create') }}" class="sid-button-primary">Buat Draft</a>
        </x-admin.page-header>
    </x-slot>

    <div class="space-y-6">
        @include('admin.partials.nav')

        <div class="sid-card p-5">
            <form method="GET" class="flex flex-wrap items-end gap-4">
                <label>
                    <span class="sid-label">Status</span>
                    <select name="status" class="sid-input min-w-[220px]">
                        <option value="">Semua status</option>
                        <option value="draft" @selected($status === 'draft')>Draft</option>
                        <option value="published" @selected($status === 'published')>Published</option>
                        <option value="archived" @selected($status === 'archived')>Archived</option>
                    </select>
                </label>
                <button type="submit" class="sid-button-primary">Filter</button>
            </form>
        </div>

        @if ($versions->isEmpty())
            @include('admin.partials.table-empty', ['title' => 'Belum ada aturan risiko', 'message' => 'Draft dan versi aturan risiko akan muncul di sini.'])
        @else
            <div class="sid-card overflow-hidden">
                <div class="sid-table-scroll">
                    <table class="min-w-[760px] text-left text-sm">
                        <thead class="bg-white/80 text-slate-500">
                            <tr>
                                <th class="px-5 py-4 font-semibold">Versi</th>
                                <th class="px-5 py-4 font-semibold">Judul</th>
                                <th class="px-5 py-4 font-semibold">Level Risiko</th>
                                <th class="px-5 py-4 font-semibold">Status</th>
                                <th class="px-5 py-4 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($versions as $version)
                                <tr class="bg-white">
                                    <td class="px-5 py-4 font-semibold text-slate-900">Versi {{ $version->version_number }}</td>
                                    <td class="px-5 py-4 text-slate-700">{{ $version->title }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $version->risk_levels_count }}</td>
                                    <td class="px-5 py-4"><x-admin.partials.status-badge :status="$version->status" /></td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-wrap gap-3">
                                            <a href="{{ route('admin.risk-rules.show', $version) }}" class="font-semibold text-[var(--color-primary)]">Preview</a>
                                            @if ($version->status->value === 'draft')
                                                <a href="{{ route('admin.risk-rules.edit', $version) }}" class="font-semibold text-slate-700">Edit</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pt-1">
                {{ $versions->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
