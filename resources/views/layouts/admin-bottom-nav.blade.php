<nav class="fixed inset-x-0 bottom-0 z-40 border-t bg-white pb-[calc(env(safe-area-inset-bottom,0px)+0.25rem)] shadow-[0_-4px_12px_rgba(149,64,158,0.04)] md:hidden">
    <div class="mx-auto grid h-16 w-full max-w-[640px] grid-cols-5">
        <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center justify-center gap-1 text-[10px] font-normal {{ request()->routeIs('admin.dashboard') ? 'text-[var(--color-primary)]' : 'text-[#82737f]' }}">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' {{ request()->routeIs('admin.dashboard') ? 1 : 0 }};">home</span>
            <span>Home</span>
        </a>

        <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center justify-center gap-1 text-[10px] font-normal {{ request()->routeIs('admin.users.*') ? 'text-[var(--color-primary)]' : 'text-[#82737f]' }}">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' {{ request()->routeIs('admin.users.*') ? 1 : 0 }};">group</span>
            <span>Pengguna</span>
        </a>

        <a href="{{ route('admin.questionnaires.index') }}" class="flex flex-col items-center justify-center gap-1 text-[10px] font-normal {{ request()->routeIs('admin.questionnaires.*') ? 'text-[var(--color-primary)]' : 'text-[#82737f]' }}">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' {{ request()->routeIs('admin.questionnaires.*') ? 1 : 0 }};">assignment</span>
            <span>Kuesioner</span>
        </a>

        <a href="{{ route('admin.education.index') }}" class="flex flex-col items-center justify-center gap-1 text-[10px] font-normal {{ request()->routeIs('admin.education.*') ? 'text-[var(--color-primary)]' : 'text-[#82737f]' }}">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' {{ request()->routeIs('admin.education.*') ? 1 : 0 }};">menu_book</span>
            <span>Artikel</span>
        </a>

        <a href="{{ route('admin.screenings.index') }}" class="flex flex-col items-center justify-center gap-1 text-[10px] font-normal {{ request()->routeIs('admin.screenings.*') ? 'text-[var(--color-primary)]' : 'text-[#82737f]' }}">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' {{ request()->routeIs('admin.screenings.*') ? 1 : 0 }};">monitor_heart</span>
            <span>Screening</span>
        </a>
    </div>
</nav>
