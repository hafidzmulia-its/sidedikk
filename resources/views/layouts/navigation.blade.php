@php($user = auth()->user())
@php($isAdmin = $user?->role === \App\Enums\UserRole::Admin)

@if ($isAdmin)
    <nav x-data="{ open: false }" class="sticky top-0 z-40 border-b bg-white">
        <div class="sid-container flex items-center justify-between gap-4 py-4">
            <a href="{{ route('admin.dashboard') }}">
                <x-application-logo />
            </a>

            <button
                type="button"
                class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border bg-white text-slate-700 shadow-sm sm:hidden"
                @click="open = ! open"
                aria-label="Buka menu"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div class="hidden items-center gap-3 sm:flex">
                <a href="{{ route('admin.dashboard') }}" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-[var(--color-primary-soft)] hover:text-[var(--color-primary-dark)]">
                    Panel Admin
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sid-button-secondary">
                        Keluar
                    </button>
                </form>
            </div>
        </div>

        <div x-show="open" x-transition x-cloak class="sid-container border-t py-3 sm:hidden">
            <div class="flex flex-col gap-2">
                <a href="{{ route('admin.dashboard') }}" class="rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-[var(--color-primary-soft)]">
                    Panel Admin
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-xl px-4 py-3 text-left text-sm font-semibold text-[var(--color-danger)] hover:bg-rose-50">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>
@else
    @php($links = [
        ['label' => 'Beranda', 'route' => 'dashboard', 'match' => 'dashboard'],
        ['label' => 'Riwayat Screening', 'route' => 'history.index', 'match' => 'history.*'],
        ['label' => 'Edukasi Kehamilan', 'route' => 'education.index', 'match' => 'education.*'],
        ['label' => 'Profil Ibu', 'route' => 'profile.edit', 'match' => 'profile.*'],
    ])

    <nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-[#f0e4f2] bg-white/95 backdrop-blur-sm">
        <div class="px-5 py-4">
            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('dashboard') }}" class="min-w-0">
                    <x-application-logo class="min-w-0" />
                </a>

                <button
                    type="button"
                    class="inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-[#eaddec] bg-white text-slate-700 shadow-[0_8px_24px_rgba(45,37,48,0.08)] transition hover:bg-[#faf5fb]"
                    @click="open = !open"
                    :aria-expanded="open.toString()"
                    :aria-label="open ? 'Tutup menu' : 'Buka menu'"
                >
                    <span class="material-symbols-outlined text-[22px]" x-text="open ? 'close' : 'menu'"></span>
                </button>
            </div>
        </div>

        <div x-show="open" x-transition x-cloak class="border-t border-[#f3e7f5] bg-white px-5 py-4">
            <div class="mb-4">
                <p class="text-sm font-semibold text-[#221825]">Menu Ibu</p>
                <p class="mt-1 text-xs text-slate-500">{{ $user?->name }}</p>
            </div>

            <div class="sid-card p-3.5">
                <div class="grid grid-cols-2 gap-2.5">
                    @foreach ($links as $link)
                        <a
                            href="{{ route($link['route']) }}"
                            class="flex min-h-0 items-center justify-center rounded-full px-2 py-2.5 text-center text-[13px] font-semibold leading-5 transition {{ request()->routeIs($link['match']) ? 'bg-[var(--color-primary)] text-white' : 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] hover:bg-white hover:shadow-sm' }}"
                        >
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="flex min-h-0 w-full items-center justify-center rounded-full border border-rose-200 px-4 py-2.5 text-[13px] font-semibold text-[var(--color-danger)] transition hover:bg-rose-50">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>
@endif
