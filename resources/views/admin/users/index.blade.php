<x-app-layout>
    <x-slot name="header">
        <x-admin.page-header
            title="Pengguna"
            description="Kelola daftar akun pengguna dan admin, lalu pantau data dasar yang terhubung dengan screening."
        />
    </x-slot>

    <div class="space-y-6">
        @include('admin.partials.nav')

        <div class="sid-card p-5">
            <form method="GET" class="grid gap-4 md:grid-cols-[1fr_220px_auto]">
                <label>
                    <span class="sid-label">Cari nama atau email</span>
                    <input type="text" name="search" value="{{ $filters['search'] }}" class="sid-input" placeholder="contoh: ibu@sid.test">
                </label>
                <label>
                    <span class="sid-label">Role</span>
                    <select name="role" class="sid-input">
                        <option value="">Semua role</option>
                        <option value="user" @selected($filters['role'] === 'user')>User</option>
                        <option value="admin" @selected($filters['role'] === 'admin')>Admin</option>
                    </select>
                </label>
                <div class="flex items-end">
                    <button type="submit" class="sid-button-primary w-full">Terapkan Filter</button>
                </div>
            </form>
        </div>

        @if ($users->isEmpty())
            @include('admin.partials.table-empty', ['title' => 'Belum ada data pengguna', 'message' => 'Pengguna yang terdaftar akan muncul di sini secara otomatis.'])
        @else
            <div class="sid-card overflow-hidden">
                <div class="sid-table-scroll">
                    <table class="min-w-[720px] text-left text-sm">
                        <thead class="bg-white/80 text-slate-500">
                            <tr>
                                <th class="px-5 py-4 font-semibold">Nama</th>
                                <th class="px-5 py-4 font-semibold">Email</th>
                                <th class="px-5 py-4 font-semibold">Role</th>
                                <th class="px-5 py-4 font-semibold">HPHT</th>
                                <th class="px-5 py-4 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($users as $user)
                                <tr class="bg-white">
                                    <td class="px-5 py-4 font-semibold text-slate-900">{{ $user->name }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $user->email }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $user->role->label() }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $user->hpht_date?->translatedFormat('d M Y') ?? '-' }}</td>
                                    <td class="px-5 py-4">
                                        <a href="{{ route('admin.users.show', $user) }}" class="font-semibold text-[var(--color-primary)]">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pt-1">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
