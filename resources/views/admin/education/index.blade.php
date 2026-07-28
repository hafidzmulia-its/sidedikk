<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-slate-500">Panel Admin SIDEDIKK</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-950">Artikel Edukasi</h1>
            </div>
            <a href="{{ route('admin.education.create') }}" class="sid-button-primary">Buat Artikel</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @include('admin.partials.nav')

        <div class="sid-card p-5">
            <form method="GET" class="grid gap-4 md:grid-cols-[1fr_220px_auto]">
                <label>
                    <span class="sid-label">Cari judul</span>
                    <input type="text" name="search" value="{{ $filters['search'] }}" class="sid-input" placeholder="Artikel ANC">
                </label>
                <label>
                    <span class="sid-label">Status</span>
                    <select name="status" class="sid-input">
                        <option value="">Semua status</option>
                        <option value="draft" @selected($filters['status'] === 'draft')>Draft</option>
                        <option value="published" @selected($filters['status'] === 'published')>Published</option>
                        <option value="unpublished" @selected($filters['status'] === 'unpublished')>Unpublished</option>
                        <option value="archived" @selected($filters['status'] === 'archived')>Archived</option>
                    </select>
                </label>
                <div class="flex items-end">
                    <button type="submit" class="sid-button-primary w-full">Filter</button>
                </div>
            </form>
        </div>

        @if ($posts->isEmpty())
            @include('admin.partials.table-empty', ['title' => 'Belum ada artikel', 'message' => 'Artikel edukasi yang dikelola admin akan muncul di sini.'])
        @else
            <div class="sid-card overflow-hidden">
                <div class="sid-table-scroll">
                    <table class="min-w-[820px] text-left text-sm">
                        <thead class="bg-white/80 text-slate-500">
                            <tr>
                                <th class="px-5 py-4 font-semibold">Judul</th>
                                <th class="px-5 py-4 font-semibold">Ringkasan</th>
                                <th class="px-5 py-4 font-semibold">Status</th>
                                <th class="px-5 py-4 font-semibold">Publikasi</th>
                                <th class="px-5 py-4 font-semibold">Cover</th>
                                <th class="px-5 py-4 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($posts as $post)
                                <tr class="bg-white align-top">
                                    <td class="px-5 py-4 font-semibold text-slate-900">{{ $post->title }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ \Illuminate\Support\Str::limit($post->excerpt, 120) }}</td>
                                    <td class="px-5 py-4"><x-admin.partials.status-badge :status="$post->status" /></td>
                                    <td class="px-5 py-4 text-slate-600">{{ $post->published_at?->translatedFormat('d M Y') ?? '-' }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $post->cover_image_path ? 'Ada' : '-' }}</td>
                                    <td class="px-5 py-4">
                                        <a href="{{ route('admin.education.edit', $post) }}" class="font-semibold text-[var(--color-primary)]">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pt-1">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
