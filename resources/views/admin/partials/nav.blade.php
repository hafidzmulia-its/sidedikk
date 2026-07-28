<div class="hidden md:block">
    <div class="sid-card p-4">
    <div class="grid grid-cols-2 gap-3 sm:flex sm:flex-wrap">
        @php
            $links = [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['label' => 'Pengguna', 'route' => 'admin.users.index'],
                ['label' => 'Screening', 'route' => 'admin.screenings.index'],
                ['label' => 'Kuesioner', 'route' => 'admin.questionnaires.index'],
                ['label' => 'Aturan Risiko', 'route' => 'admin.risk-rules.index'],
                ['label' => 'Edukasi', 'route' => 'admin.education.index'],
            ];
        @endphp

        @foreach ($links as $link)
            <a
                href="{{ route($link['route']) }}"
                class="flex items-center justify-center rounded-full px-4 py-3 text-center text-sm font-semibold transition {{ request()->routeIs($link['route']) || request()->routeIs(str_replace('.index', '.*', $link['route'])) ? 'bg-[var(--color-primary)] text-white' : 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] hover:bg-white hover:shadow-sm' }}"
            >
                {{ $link['label'] }}
            </a>
        @endforeach
    </div>
</div>
</div>
