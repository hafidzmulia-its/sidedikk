<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-slate-500">Panel Admin SIDEDIKK</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-950">Detail Pengguna</h1>
            </div>
            <a href="{{ route('admin.users.index') }}" class="sid-button-secondary">Kembali</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @include('admin.partials.nav')

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="sid-card p-6">
                <p class="text-sm font-medium text-slate-500">Informasi Dasar</p>
                <div class="mt-5 space-y-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Nama</p>
                        <p class="mt-1 text-lg font-bold text-slate-950">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Email</p>
                        <p class="mt-1 text-base text-slate-700">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Role</p>
                        <p class="mt-1 text-base text-slate-700">{{ $user->role->label() }}</p>
                    </div>
                </div>
            </div>

            <div class="sid-card p-6">
                <p class="text-sm font-medium text-slate-500">Kehamilan & Aktivitas</p>
                <div class="mt-5 space-y-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Usia</p>
                        <p class="mt-1 text-base text-slate-700">{{ $user->age ?? '-' }} tahun</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">HPHT</p>
                        <p class="mt-1 text-base text-slate-700">{{ $user->hpht_date?->translatedFormat('d F Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Screening Selesai</p>
                        <p class="mt-1 text-base text-slate-700">{{ $completedScreeningsCount }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
