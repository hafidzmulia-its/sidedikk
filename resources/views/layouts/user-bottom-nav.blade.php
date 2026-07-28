<nav class="fixed inset-x-0 bottom-0 z-40 border-t bg-white pb-[calc(env(safe-area-inset-bottom,0px)+0.25rem)] shadow-[0_-4px_12px_rgba(149,64,158,0.04)]">
    <div class="mx-auto grid h-16 w-full max-w-[480px] grid-cols-4">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center gap-1 text-[10px] font-normal {{ request()->routeIs('dashboard') ? 'text-[var(--color-primary)]' : 'text-[#82737f]' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('dashboard') ? '' : '' }}" style="font-variation-settings: 'FILL' {{ request()->routeIs('dashboard') ? 1 : 0 }};">home</span>
            <span>Beranda</span>
        </a>

        <a href="{{ route('history.index') }}" class="flex flex-col items-center justify-center gap-1 text-[10px] font-normal {{ request()->routeIs('history.*') ? 'text-[var(--color-primary)]' : 'text-[#82737f]' }}">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' {{ request()->routeIs('history.*') ? 1 : 0 }};">history</span>
            <span>Riwayat</span>
        </a>

        <a href="{{ route('education.index') }}" class="flex flex-col items-center justify-center gap-1 text-[10px] font-normal {{ request()->routeIs('education.*') ? 'text-[var(--color-primary)]' : 'text-[#82737f]' }}">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' {{ request()->routeIs('education.*') ? 1 : 0 }};">menu_book</span>
            <span>Edukasi</span>
        </a>

        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center gap-1 text-[10px] font-normal {{ request()->routeIs('profile.*') ? 'text-[var(--color-primary)]' : 'text-[#82737f]' }}">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' {{ request()->routeIs('profile.*') ? 1 : 0 }};">person</span>
            <span>Profil</span>
        </a>
    </div>
</nav>
